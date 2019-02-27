<?php
use Tribe__Date_Utils as Date;
/**
 * Shows an admin notice for Timezones
 * (When using UTC and on TEC Pages or WordPress > General Settings)
 */
class Tribe__Events__Admin__Notice__Timezones {

	/**
	 * Notice Slug on the user options
	 *
	 * @since  TBD
	 * @var string
	 */
	private $slug = 'events-utc-timezone';

	public function hook() {
		$date = $this->get_current_reset_date();
		$slug = $this->slug;
		/**
		 * Allows users to completely deactivate the resetting of the Day Light savings notice
		 *
		 * @since  TBD
		 *
		 * @param  bool
		 */
		$should_reset = apply_filters( 'tribe_events_admin_notice_daylight_savings_reset_notice', true );

		// If we have a date append to the Slug
		if ( $date ) {
			$slug .= '-' . $date;
		}

		tribe_notice(
			$slug,
			[ $this, 'notice' ],
			[
				'type'    => 'warning',
				'dismiss' => 1,
				'wrap'    => 'p',
			],
			[ $this, 'should_display' ]
		);

	}

	/**
	 * Fetches the date in which the Notice had it's reset
	 *
	 * @since  TBD
	 *
	 * @return string|null
	 */
	public function get_current_reset_date() {
		$dates = $this->get_reset_dates();
		$today = date( Date::DBDATEFORMAT );

		foreach ( $dates as $key => $date ) {
			if ( $date <= $today ) {
				return $date;
			}
		}

		return null;
	}

	/**
	 * Which dates this Notice gets reset
	 *
	 * @since  TBD
	 *
	 * @return array
	 */
	public function get_reset_dates() {
		$dates[] = date( Date::DBDATEFORMAT, strtotime( 'last sunday of february' ) );
		$dates[] = date( Date::DBDATEFORMAT, strtotime( 'third sunday of march' ) );
		$dates[] = date( Date::DBDATEFORMAT, strtotime( 'third sunday of october' ) );
		$dates[] = date( Date::DBDATEFORMAT, strtotime( 'second sunday of november' ) );
		return $dates;
	}

	/**
	 * Checks if we are in an TEC page or over
	 * the WordPress > Settings > General
	 *
	 * @since  4.6.17
	 *
	 * @return boolean
	 */
	public function should_display() {
		global $pagenow;

		// Bail if the site isn't using UTC
		if ( ! $this->is_utc_timezone() ) {
			return false;
		}

		// It should display if we're on a TEC page or
		// over Settings > General
		return tribe( 'admin.helpers' )->is_screen() || 'options-general.php' === $pagenow;
	}

	/**
	 * Checks if the site is using UTC Timezone Options
	 *
	 * @since  4.6.17
	 *
	 * @return boolean
	 */
	public function is_utc_timezone() {
		// If the site is using UTC or UTC manual offset
		return strpos( Tribe__Timezones::wp_timezone_string(), 'UTC' ) !== false;
	}

	/**
	 * HTML for the notice for sites using UTC Timezones.
	 *
	 * @since  4.6.17
	 *
	 * @return string
	 */
	public function notice() {
		// Bail if the user is not admin or can manage plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}

		$text = [];
		$current_utc = Tribe__Timezones::wp_timezone_string();

		$url = 'http://m.tri.be/1acz';
		$link = sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( $url ),
			esc_html__( 'Read more', 'the-events-calendar' )
		);

		$text[] = __( 'Daylight Saving Time could impact your events! To show the right time for your location, be sure to correctly configure each event as well as WordPress itself.', 'the-events-calendar' );
		$text[] = __( 'For best results, we recommend you use a geographic timezone such as "America/Los Angeles" instead of an offset such as "%2$s". %1$s', 'the-events-calendar' );

		return sprintf( implode( '<br />', $text ), $link, $current_utc );

	}
}
