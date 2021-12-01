<?php
/**
 * The base implementation for the Views v2 query controllers.
 *
 * @package Tribe\Events\Views\V2\iCalendar
 * @since 5.12.0
 */

namespace Tribe\Events\Views\V2\iCalendar\Links;

use Tribe__Date_Utils as Dates;
use \Tribe\Events\Views\V2\View as View;

/**
 * Class Abstract_Link
 *
 * @package Tribe\Events\Views\V2\iCalendar
 * @since 5.12.0
 */
abstract class Link_Abstract implements Link_Interface {

	/**
	 * The (translated) text/label for the link.
	 *
	 * @since 5.12.0
	 *
	 * @var string
	 */
	public $label;

	/**
	 * The (translated) text/label for the link.
	 *
	 * @since 5.12.0
	 *
	 * @var string
	 */
	public $single_label;

	/**
	 * Whether to display the link or not.
	 *
	 * @since 5.12.0
	 *
	 * @var boolean
	 */
	public $display = true;

	/**
	 * the link provider slug.
	 *
	 * @since 5.12.0
	 *
	 * @var string
	 */
	public static $slug;

	/**
	 * Registers the objects and filters required by the provider to manage subscribe links.
	 *
	 * @since 5.12.0
	 */
	public function register() {
		add_filter( 'tec_views_v2_subscribe_links', [ $this, 'filter_tec_views_v2_subscribe_links'], 10, 2 );
		add_filter( 'tec_views_v2_single_subscribe_links', [ $this, 'filter_tec_views_v2_single_subscribe_links' ], 10, 2 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_tec_views_v2_subscribe_links( $subscribe_links, $view ) {
		$subscribe_links[static::get_slug()] = $this;

		return $subscribe_links;
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_tec_views_v2_single_subscribe_links( $links, $view ) {
		$class = sanitize_html_class( 'tribe-events-' . static::get_slug() );
		$links[] = '<a class="tribe-events-button ' . $class
				. '" href="' . esc_url( $this->get_uri( $view ) )
				. '" title="' . esc_attr( $this->get_single_label( $view ) )
				. '">+ ' . esc_html( $this->get_single_label( $view ) ) . '</a>';

		return $links;
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_visible( $view ) {
		return $this->display;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_label( $view ) {
		/**
		 * Allows filtering of the labels for the Calendar view labels.
		 *
		 * @param string                      $label    The label that will be displayed.
		 * @param Link_Abstract               $link_obj The link object the label is for.
		 * @param \Tribe\Events\Views\V2\View $view     The current View object.
		 *
		 * @return string $label The label that will be displayed.
		 */
		return apply_filters( 'tec_views_v2_subscribe_links_' . self::get_slug() . '_label', $this->label, $this, $view );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_single_label( $view ) {
		/**
		 * Allows filtering of the labels for the Single Event view labels.
		 *
		 * @param string                      $label    The label that will be displayed.
		 * @param Link_Abstract               $link_obj The link object the label is for.
		 * @param \Tribe\Events\Views\V2\View $view     The current View object.
		 *
		 * @return string $label The label that will be displayed.
		 */
		return apply_filters( 'tec_views_v2_single_subscribe_links_' . self::get_slug() . '_label', $this->single_label, $this, $view );
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug() {
		return static::$slug;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_visibility( bool $visible ) {
		$this->display = $visible;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_uri( $view ) {
		// If we're on a Single Event view, let's bypass the canonical function call and logic.
		$feed_url = empty( $view ) ? tribe_get_single_ical_link() : $view->get_context()->get( 'single_ical_link', false );

		if ( empty( $feed_url ) && ! empty( $view ) ) {
			$feed_url = $this->get_canonical_ics_feed_url( $view );
		}

		$feed_url = str_replace( [ 'http://', 'https://' ], 'webcal://', $feed_url );

		return $feed_url;
	}

	/**
	 * Retrieve the iCal Feed URL with current context parameters.
	 *
	 * Both iCal and gCal URIs can be built from the Feed URL which simply
	 * points to a canonical URL that the generator can parse
	 * via `tribe_get_global_query_object` and spew out results in the
	 * ICS format.
	 *
	 * This is exactly what \Tribe__Events__iCal::do_ical_template does
	 * and lets it generate from a less vague and a more context-bound URL
	 * for more granular functionality. This lets us have shortcode support
	 * among other things.
	 *
	 * We strip some of the things that we don't need for subscriptions
	 * like end dates, view types, etc., ignores pagination and always returns
	 * fresh future events.
	 *
	 * The URL generated is also inert to the Permalink and Rewrite Rule settings
	 * in WordPress, so it will work out of the box on any website, even if
	 * the settings are changed or break.
	 *
	 * @param \Tribe\Events\Views\V2\View $view The View we're being called from.
	 *
	 * @return string The iCal Feed URI.
	 */
	protected function get_canonical_ics_feed_url( View $view ) {
		$view_url_args = $view->get_url_args();

		// Some date magic.
		if ( isset( $view_url_args['eventDate'] ) ) {
			// Subscribe from the calendar date (pagination, shortcode calendars, etc).
			$view_url_args['tribe-bar-date'] = $view_url_args['eventDate'];
		} else {
			// Subscribe from today (default calendar view).
			$view_url_args['tribe-bar-date'] = Dates::build_date_object()->format( Dates::DBDATEFORMAT );
		}



		// Clean query params to only contain canonical arguments.
		$canonical_args = [ 'post_type', 'tribe-bar-date', 'tribe_events_cat', 'post_tag' ];

		/**
		 * Allows other plugins to alter what gets passed to the subscribe link.
		 *
		 * @since 5.12.0
		 *
		 * @param array<string>               $canonical_args A list of "passthrough" argument keys.
		 * @param \Tribe\Events\Views\V2\View $view           The View we're being called from.
		 *
		 * @return array<string> $canonical_args The modified list of "passthrough" argument keys.
		 */
		$canonical_args = apply_filters( 'tec_views_v2_subscribe_links_canonical_args', $canonical_args, $view );

		// This array will become the args we pass to `add_query_arg()`
		$passthrough_args = [];

		foreach ( $view_url_args as $arg => $value ) {
			if ( in_array( $arg, $canonical_args, true ) ) {
				$passthrough_args[ $arg ] = $view_url_args[ $arg ];
			}
		}

		// iCalendarize!
		$passthrough_args['ical'] = 1;

		// Tidy.
		$passthrough_args = array_filter( $passthrough_args );

		/**
		 * Allows other plugins to alter the query args that get passed to the subscribe link.
		 *
		 * @since 5.12.0
		 *
		 * @param array<string|mixed>         $passthrough_args The arguments used to build the ical links.
		 * @param array<string>               $canonical_args   A list of allowed argument keys.
		 * @param \Tribe\Events\Views\V2\View $view             The View we're being called from.
		 *
		 * @return array<string|mixed>        $passthrough_args The modified list of arguments used to build the ical links.
		 */
		$passthrough_args = apply_filters( 'tec_views_v2_subscribe_links_url_args', $passthrough_args, $view );

		return add_query_arg( urlencode_deep( $passthrough_args ), home_url( '/' ) );
	}
}