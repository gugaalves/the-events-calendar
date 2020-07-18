<?php
/**
 * Provides common View v2 utilities.
 *
 * @since   4.9.4
 * @package Tribe\Events\Views\V2\Utils
 */
namespace Tribe\Events\Views\V2\Utils;

use Tribe__Utils__Array as Arr;

/**
 * Class Utils View
 * @since   4.9.4
 * @package Tribe\Events\Views\V2\Utils
 */
class View {
	/**
	 * Reads a view data entry from the current request.
	 *
	 * @since 4.9.4
	 *
	 * @param string|array $indexes One ore more indexes to check for in the view data.
	 * @param null|mixed   $default The default value to return if the data is not found.
	 *
	 * @return mixed|null The view data, if found, or a default value.
	 */
	public static function get_data( $indexes, $default = null ) {
		$found = Arr::get_first_set(
			tribe_get_request_var( 'view_data', [] ),
			(array) $indexes,
			$default
		);

		return empty( $found ) || $default === $found ? $default : $found;
	}

	/**
	 * Based on the `permalink_structure` determines which variable the view should read `event_display_mode` for past
	 * URL management.
	 *
	 * @since 5.0.0
	 *
	 * @return string URL Query Variable Key
	 */
	public static function get_past_event_display_key() {
		$event_display_key = 'eventDisplay';

		// When dealing with "Plain Permalink" we need to move `past` into a separate url argument.
		if ( ! get_option( 'permalink_structure' ) ) {
			$event_display_key = 'tribe_event_display';
		}

		return $event_display_key;
	}

	/**
	 * Cleans the View data that will be printed by the `components/data.php` template to avoid its mangling.
	 *
	 * By default, the View data is a copy of the View template variables, to avoid the mangling of the JSON data
	 * some entries of the data might require to be removed, some might require to be formatted or escaped. E.g. the
	 * View JSON-LD data, a <script>, should not be printed as-is in the data <script> tag to avoid later escaping
	 * functions (e.g. `wptexturize`) mangling it.
	 *
	 * @since TBD
	 *
	 * @param array<string,string|array> $view_data The initial View data.
	 *
	 * @return array<string,string|array> The filtered View data, some entries removed from it to avoid the data script
	 *                                    being mangled by escaping and texturizing functions running on it.
	 */
	public static function clean_data( $view_data ) {
		if ( ! is_array( $view_data ) ) {
			return $view_data;
		}

		if ( isset( $view_data['json_ld_data'] ) ) {
			// Include the data in escaped form; non-escaped form is printed by the `components/json-ld-data` template.
			$view_data['json_ld_data'] = esc_html( $view_data['json_ld_data'] );
		}

		return $view_data;
	}
}
