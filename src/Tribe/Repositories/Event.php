<?php
/**
 * The main ORM/Repository class for events.
 *
 * @since TBD
 */

/**
 * Class Tribe__Events__Repositories__Event
 *
 *
 * @since TBD
 */
class Tribe__Events__Repositories__Event extends Tribe__Repository {

	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @var string
	 */
	protected $filter_name = 'events';

	/**
	 * Tribe__Events__Repositories__Event constructor.
	 *
	 * Sets up the repository default parameters and schema.
	 *
	 * @since TBD
	 */
	public function __construct() {
		parent::__construct();

		$this->default_args = array(
			'post_type'                    => Tribe__Events__Main::POSTTYPE,
			// We'll be handling the dates, let's mark the query as a non-filtered one.
			'tribe_suppress_query_filters' => true,
		);

		$this->schema = array_merge( $this->schema, array(
			'starts_before'           => array( $this, 'filter_by_starts_before' ),
			'starts_after'            => array( $this, 'filter_by_starts_after' ),
			'starts_between'          => array( $this, 'filter_by_starts_between' ),
			'ends_before'             => array( $this, 'filter_by_ends_before' ),
			'ends_after'              => array( $this, 'filter_by_ends_after' ),
			'ends_between'            => array( $this, 'filter_by_ends_between' ),
			'starts_and_ends_between' => array( $this, 'filter_by_starts_and_ends_between' ),
			'runs_between'            => array( $this, 'filter_by_runs_between' ),
			'all_day'                 => array( $this, 'filter_by_all_day' ),
			'multiday'                => array( $this, 'filter_by_multiday' ),
			'on_calendar_grid'        => array( $this, 'filter_by_on_calendar_grid' ),
			'timezone'                => array( $this, 'filter_by_timezone' ),
			'cost_currency_symbol'    => array( $this, 'filter_by_cost_currency_symbol' ),
			'cost'                    => array( $this, 'filter_by_cost' ),
			'cost_between'            => array( $this, 'filter_by_cost_between' ),
			'cost_less_than'          => array( $this, 'filter_by_cost_less_than' ),
			'cost_greater_than'       => array( $this, 'filter_by_cost_greater_than' ),
		) );
	}

	/**
	 * Filters the event by their all-day status.
	 *
	 * @since TBD
	 *
	 * @param bool $all_day Whether the events should be all-day or not.
	 *
	 * @return array|null An array of query arguments or null if modified with internal methods.
	 */
	public function filter_by_all_day( $all_day = true ) {
		if ( (bool) $all_day ) {
			$this->by( 'meta_equals', '_EventAllDay', 'yes' );

			return null;
		}

		return array(
			'meta_query' => array(
				'by-all-day' => array(
					'not-exists' => array(
						'key'     => '_EventAllDay',
						'compare' => 'NOT EXISTS',
						'value'   => '#',
					),
					'relation'   => 'OR',
					'is-not-yes' => array(
						'key'     => '_EventAllDay',
						'compare' => '!=',
						'value'   => 'yes',
					),
				),
			),
		);
	}

	/**
	 * Filters events whose start date occurs before the provided date; fetch is not inclusive.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $datetime A `strtotime` parse-able string, a DateTime object or
	 *                                      a timestamp.
	 * @param string|DateTimeZone $timezone A timezone string, UTC offset or DateTimeZone object;
	 *                                      defaults to the site timezone; this parameter is ignored
	 *                                      if the `$datetime` parameter is a DatTime object.
	 *
	 * @return array An array of arguments that should be added to the WP_Query object.
	 */
	public function filter_by_starts_before( $datetime, $timezone = null ) {
		$date = Tribe__Date_Utils::build_date_object( $datetime, $timezone )
		                         ->setTimezone( new DateTimeZone( 'UTC' ) );

		return array(
			'meta_query' => array(
				'ends-before' => array(
					'key'     => '_EventStartDateUTC',
					'compare' => '<',
					'value'   => $date->format( 'Y-m-d H:i:s' ),
					'type'    => 'DATETIME',
				),
			),
		);
	}

	/**
	 * Filters events whose end date occurs before the provided date; fetch is not inclusive.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $datetime A `strtotime` parse-able string, a DateTime object or
	 *                                      a timestamp.
	 * @param string|DateTimeZone $timezone A timezone string, UTC offset or DateTimeZone object;
	 *                                      defaults to the site timezone; this parameter is ignored
	 *                                      if the `$datetime` parameter is a DatTime object.
	 *
	 * @return array An array of arguments that should be added to the WP_Query object.
	 */
	public function filter_by_ends_before( $datetime, $timezone = null ) {
		$date = Tribe__Date_Utils::build_date_object( $datetime, $timezone )
		                         ->setTimezone( new DateTimeZone( 'UTC' ) );

		return array(
			'meta_query' => array(
				'ends-before' => array(
					'key'     => '_EventEndDateUTC',
					'compare' => '<',
					'value'   => $date->format( 'Y-m-d H:i:s' ),
					'type'    => 'DATETIME',
				),
			),
		);
	}

	/**
	 * Filters events whose start date occurs after the provided date; fetch is not inclusive.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $datetime A `strtotime` parse-able string, a DateTime object or
	 *                                      a timestamp.
	 * @param string|DateTimeZone $timezone A timezone string, UTC offset or DateTimeZone object;
	 *                                      defaults to the site timezone; this parameter is ignored
	 *                                      if the `$datetime` parameter is a DatTime object.
	 *
	 * @return array An array of arguments that should be added to the WP_Query object.
	 */
	public function filter_by_starts_after( $datetime, $timezone = null ) {
		$date = Tribe__Date_Utils::build_date_object( $datetime, $timezone )
		                         ->setTimezone( new DateTimeZone( 'UTC' ) );

		return array(
			'meta_query' => array(
				'ends-after' => array(
					'key'     => '_EventStartDateUTC',
					'compare' => '>',
					'value'   => $date->format( 'Y-m-d H:i:s' ),
					'type'    => 'DATETIME',
				),
			),
		);
	}

	/**
	 * Filters events whose end date occurs after the provided date; fetch is not inclusive.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $datetime A `strtotime` parse-able string, a DateTime object or
	 *                                      a timestamp.
	 * @param string|DateTimeZone $timezone A timezone string, UTC offset or DateTimeZone object;
	 *                                      defaults to the site timezone; this parameter is ignored
	 *                                      if the `$datetime` parameter is a DatTime object.
	 *
	 * @return array An array of arguments that should be added to the WP_Query object.
	 */
	public function filter_by_ends_after( $datetime, $timezone = null ) {
		$date = Tribe__Date_Utils::build_date_object( $datetime, $timezone )
		                         ->setTimezone( new DateTimeZone( 'UTC' ) );

		return array(
			'meta_query' => array(
				'ends-after' => array(
					'key'     => '_EventEndDateUTC',
					'compare' => '>',
					'value'   => $date->format( 'Y-m-d H:i:s' ),
					'type'    => 'DATETIME',
				),
			),
		);
	}

	/**
	 * Filters events whose start date occurs between a set of dates; fetch is inclusive.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $start_datetime A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTime|int $end_datetime   A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTimeZone $timezone       A timezone string, UTC offset or DateTimeZone object;
	 *                                            defaults to the site timezone; this parameter is ignored
	 *                                            if the `$datetime` parameter is a DatTime object.
	 */
	public function filter_by_starts_between( $start_datetime, $end_datetime, $timezone = null ) {
		$utc = new DateTimeZone( 'UTC' );

		$lower = Tribe__Date_Utils::build_date_object( $start_datetime, $timezone )->setTimezone( $utc );
		$upper = Tribe__Date_Utils::build_date_object( $end_datetime, $timezone )->setTimezone( $utc );

		$this->by( 'meta_between', '_EventStartDateUTC', array(
			$lower->format( 'Y-m-d H:i:s' ),
			$upper->format( 'Y-m-d H:i:s' ),
		), 'DATETIME' );
	}

	/**
	 * Filters events whose end date occurs between a set of dates; fetch is inclusive.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $start_datetime A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTime|int $end_datetime   A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTimeZone $timezone       A timezone string, UTC offset or DateTimeZone object;
	 *                                            defaults to the site timezone; this parameter is ignored
	 *                                            if the `$datetime` parameter is a DatTime object.
	 */
	public function filter_by_ends_between( $start_datetime, $end_datetime, $timezone = null ) {
		$utc = new DateTimeZone( 'UTC' );

		$lower = Tribe__Date_Utils::build_date_object( $start_datetime, $timezone )->setTimezone( $utc );
		$upper = Tribe__Date_Utils::build_date_object( $end_datetime, $timezone )->setTimezone( $utc );

		$this->by( 'meta_between', '_EventEndDateUTC', array(
			$lower->format( 'Y-m-d H:i:s' ),
			$upper->format( 'Y-m-d H:i:s' ),
		), 'DATETIME' );
	}

	/**
	 * Filters events to include only those that match the provided multi day state.
	 *
	 * Please note that an event might be multi-day in its timezone but not in another;
	 * this filter will make the check on the event times localized to the event timezone.
	 * Furthermore the end of day cutoff is taken into account so, given a cutoff of 10PM
	 * an event starting at 10:30PM and ending at 11AM is not multi-day.
	 *
	 * @since TBD
	 *
	 * @param bool $multiday Whether to filter by events that are or not multi-day.
	 */
	public function filter_by_multiday( $multiday = true ) {
		global $wpdb;

		$this->filter_query->join( "LEFT JOIN {$wpdb->postmeta} multiday_start_date
			ON ( {$wpdb->posts}.ID = multiday_start_date.post_id 
			AND multiday_start_date.meta_key = '_EventStartDate' )" );
		$this->filter_query->join( "LEFT JOIN {$wpdb->postmeta} multiday_end_date 
			ON ( {$wpdb->posts}.ID = multiday_end_date.post_id
			AND multiday_end_date.meta_key = '_EventEndDate' )" );

		// We're interested in the time only.
		$end_of_day_cutoff = tribe_end_of_day( 'today', 'H:i:s' );

		if ( '23:59:59' === $end_of_day_cutoff ) {
			/*
			 * An event is considered multi-day when the end date is not the same as the start date when
			 * using the "natural" end-of-day cutoff.
			 */
			$compare = $multiday ? '!=' : '=';
			$this->filter_query->where(
				"DATE( multiday_end_date.meta_value ) {$compare} DATE( multiday_start_date.meta_value )"
			);
		} else {
			/*
			 * An event is considered multi-day when the end date is after the end-of-day cutoff of the start date.
			 * Since the cut-off moves forward from midnight add 1 day to the start date.
			 */
			$compare = $multiday ? '>' : '<=';
			$this->filter_query->where(
				"multiday_end_date.meta_value {$compare} DATE_FORMAT( DATE_ADD( multiday_start_date.meta_value, INTERVAL 1 DAY ) , '%Y-%m-%d {$end_of_day_cutoff}' )"
			);
		}
	}

	/**
	 * Filters events to include only those events that appear on the given month’s calendar grid.
	 *
	 * @since TBD
	 *
	 * @param int $month The month to display.
	 * @param int $year  The year to display.
	 *
	 * @return array|null An array of arguments that should be added to the query or `null`
	 *                    if the arguments are not valid (thus the filter will be ignored).
	 */
	public function filter_by_on_calendar_grid( $month, $year ) {
		$year_month_string = "{$year}-{$month}";

		if ( ! Tribe__Date_Utils::is_valid_date( $year_month_string ) ) {
			/*
			 * Months and years are known but, at runtime, the client code might get, or pass,
			 * them wrong. In that case this filter will not be applied.
			 */
			return null;
		}

		$start = Tribe__Events__Template__Month::calculate_first_cell_date( $year_month_string );
		$end   = Tribe__Events__Template__Month::calculate_final_cell_date( $year_month_string );

		return $this->filter_by_runs_between( $start, tribe_end_of_day( $end ) );
	}

	/**
	 * Filters events to include only those events that are running between two dates.
	 *
	 * An event is running between two dates when its start date or end date are between
	 * the two dates.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $start_datetime A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTime|int $end_datetime   A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTimeZone $timezone       A timezone string, UTC offset or DateTimeZone object;
	 *                                            defaults to the site timezone; this parameter is ignored
	 *                                            if the `$datetime` parameter is a DatTime object.
	 *
	 * @return array An array of arguments that should be added to the WP_Query object.
	 */
	public function filter_by_runs_between( $start_datetime, $end_datetime, $timezone = null ) {
		$start_date = Tribe__Date_Utils::build_date_object( $start_datetime, $timezone )
		                               ->setTimezone( new DateTimeZone( 'UTC' ) )
		                               ->format( 'Y-m-d H:i:s' );
		$end_date   = Tribe__Date_Utils::build_date_object( $end_datetime, $timezone )
		                               ->setTimezone( new DateTimeZone( 'UTC' ) )
		                               ->format( 'Y-m-d H:i:s' );

		return array(
			'meta_query' => array(
				'runs-between' => array(
					'starts'   => array(
						'after-the-start' => array(
							'key'     => '_EventStartDateUTC',
							'value'   => $start_date,
							'compare' => '>=',
							'type'    => 'DATETIME',
						),
						'relation'        => 'AND',
						'before-the-end'  => array(
							'key'     => '_EventStartDateUTC',
							'value'   => $end_date,
							'compare' => '<=',
							'type'    => 'DATETIME',
						),
					),
					'relation' => 'OR',
					'ends'     => array(
						'after-the-start' => array(
							'key'     => '_EventEndDateUTC',
							'value'   => $start_date,
							'compare' => '>=',
							'type'    => 'DATETIME',
						),
						'relation'        => 'AND',
						'before-the-end'  => array(
							'key'     => '_EventEndDateUTC',
							'value'   => $end_date,
							'compare' => '<=',
							'type'    => 'DATETIME',
						),
					),
				),
			),
		);
	}

	/**
	 * Filters events the given timezone.
	 *
	 * UTC, UTC+0, and UTC-0 should be parsed as the same timezone.
	 *
	 * @since TBD
	 *
	 * @param string|DateTimeZone $timezone A timezone string or object.
	 *
	 * @return array An array of arguments to apply to the query.
	 */
	public function filter_by_timezone( $timezone ) {
		if ( $timezone instanceof DateTimeZone ) {
			$timezone = $timezone->getName();
		}

		$is_utc = preg_match( '/^UTC((\\+|-)0)*$/i', $timezone );

		if ( $is_utc ) {
			return array(
				'meta_query' => array(
					'by-timezone' => array(
						'key'     => '_EventTimezone',
						'compare' => 'IN',
						'value'   => array( 'UTC', 'UTC+0', 'UTC-0' ),
					),
				),
			);
		}

		return array(
			'meta_query' => array(
				'by-timezone' => array(
					'key'   => '_EventTimezone',
					'value' => $timezone,
				),
			),
		);
	}

	/**
	 * Filters events whose start and end dates occur between a set of dates.
	 *
	 * Fetch is inclusive.
	 *
	 * @since TBD
	 *
	 * @param string|DateTime|int $start_datetime A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTime|int $end_datetime   A `strtotime` parse-able string, a DateTime object or
	 *                                            a timestamp.
	 * @param string|DateTimeZone $timezone       A timezone string, UTC offset or DateTimeZone object;
	 *                                            defaults to the site timezone; this parameter is ignored
	 *                                            if the `$datetime` parameter is a DatTime object.
	 *
	 * @return array An array of arguments that should be added to the WP_Query object.
	 */
	public function filter_by_starts_and_ends_between( $start_datetime, $end_datetime, $timezone = null ) {
		$start_date = Tribe__Date_Utils::build_date_object( $start_datetime, $timezone )
		                               ->setTimezone( new DateTimeZone( 'UTC' ) )
		                               ->format( 'Y-m-d H:i:s' );
		$end_date   = Tribe__Date_Utils::build_date_object( $end_datetime, $timezone )
		                               ->setTimezone( new DateTimeZone( 'UTC' ) )
		                               ->format( 'Y-m-d H:i:s' );

		$interval = array( $start_date, $end_date );

		return array(
			'meta_query' => array(
				'starts-ends-between' => array(
					'starts-between' => array(
						'key'     => '_EventStartDateUTC',
						'value'   => $interval,
						'compare' => 'BETWEEN',
						'type'    => 'DATETIME',
					),
					'relation'       => 'AND',
					'ends-between'   => array(
						'key'     => '_EventEndDateUTC',
						'value'   => $interval,
						'compare' => 'BETWEEN',
						'type'    => 'DATETIME',
					),
				),
			),
		);
	}

	/**
	 * Filters events that have a specific cost currency symbol.
	 *
	 * Events with a cost of `0` but a currency symbol set will be fetched when fetching
	 * by their symbols.
	 *
	 * @since TBD
	 *
	 * @param string|array $symbol One or more currency symbols or currency ISO codes. E.g.
	 *                             "$" and "USD".
	 *
	 * @return array An array of arguments that will be added to the current query.
	 */
	public function filter_by_cost_currency_symbol( $symbol ) {
		return array(
			'meta_query' => array(
				'by-cost-currency-symbol' => array(
					'key'     => '_EventCurrencySymbol',
					'value'   => array_unique( (array) $symbol ),
					'compare' => 'IN'
				)
			)
		);
	}

	/**
	 * Filters events that have a cost relative to the given value based on the $comparator.
	 * If Event Tickets is active, rather than looking at the event cost, all tickets attached
	 * to the event should used to reference cost; the event cost meta will be ignored.
	 *
	 * Providing the symbol parameter should limit event results to only those events whose cost is relative to
	 * the value AND the currency symbol matches. This way you can select posts that have a cost of 5 USD and
	 * not accidentally get events with 5 EUR.
	 *
	 * @since TBD
	 *
	 * @param float|array $value       The cost to use for the comparison; in the case of `BETWEEN`, `NOT BETWEEN`,
	 *                                 `IN` and `NOT IN` operators this value should be an array.
	 * @param string      $operator    Teh comparison operator to use for the comparison, one of `<`, `<=`, `>`, `>=`,
	 *                                 `=`, `BETWEEN`, `NOT BETWEEN`, `IN`, `NOT IN`.
	 * @param string      $symbol      The desired currency symbol or symbols; this symbol can be a currency ISO code,
	 *                                 e.g. "USD" for U.S. dollars, or a currency symbol, e.g. "$".
	 *                                 In the latter case results will include any event with the matching currency
	 *                                 symbol, this might lead to ambiguous results.
	 *
	 * @return array An array of query arguments that will be added to the main query.
	 *
	 * @throws Tribe__Repository__Usage_Error If the comparison operator is not supported of is using the `BETWEEN`,
	 *                                        `NOT BETWEEN` operators without passing a two element array `$value`.
	 */
	public function filter_by_cost( $value, $operator = '=', $symbol = null ) {
		if ( ! in_array( $operator, array(
			'<',
			'<=',
			'>',
			'>=',
			'=',
			'!=',
			'BETWEEN',
			'NOT BETWEEN',
			'IN',
			'NOT IN'
		) ) ) {
			throw Tribe__Repository__Usage_Error::because_this_comparison_operator_is_not_supported( $operator, 'filter_by_cost' );
		}

		if ( in_array( $operator, array(
				'BETWEEN',
				'NOT BETWEEN'
			) ) && ! ( is_array( $value ) && 2 === count( $value ) ) ) {
			throw Tribe__Repository__Usage_Error::because_this_comparison_operator_requires_an_value_of_type( $operator, 'filter_by_cost', 'array' );
		}

		if ( in_array( $operator, array( 'IN', 'NOT IN' ) ) ) {
			$value = (array) $value;
		}

		$operator_name  = Tribe__Utils__Array::get( self::$comparison_operators, $operator, '' );
		$meta_query_key = 'by-cost-' . $operator_name;

		$meta_query_entry = array(
			$meta_query_key => array(
				'key'     => '_EventCost',
				'value'   => $value,
				'compare' => $operator,
				'type'    => 'NUMERIC',
			)
		);

		if ( null !== $symbol ) {
			$meta_query_entry = array_merge( $meta_query_entry, $this->filter_by_cost_currency_symbol( $symbol ) );
		}

		return array( 'meta_query' => $meta_query_entry );
	}

	/**
	 * Filters events that have a cost between two given values.
	 *
	 * Cost search is inclusive.
	 *
	 * @since TBD
	 *
	 * @param      float $low    The lower value of the search interval.
	 * @param      float $high   The high value of the search interval.
	 * @param string     $symbol The desired currency symbol or symbols; this symbol can be a currency ISO code,
	 *                           e.g. "USD" for U.S. dollars, or a currency symbol, e.g. "$".
	 *                           In the latter case results will include any event with the matching currency symbol,
	 *                           this might lead to ambiguous results.
	 *
	 * @return array An array of query arguments that will be added to the main query.
	 */
	public function filter_by_cost_between( $low, $high, $symbol = null ) {
		return $this->by( 'cost', array( $low, $high ), 'BETWEEN', $symbol );
	}

	/**
	 * Filters events that have a cost greater than the given value.
	 *
	 * Cost search is NOT inclusive.
	 *
	 * @since TBD
	 *
	 * @param float  $value      The cost to use for the comparison.
	 * @param string $symbol     The desired currency symbol or symbols; this symbol can be a currency ISO code,
	 *                           e.g. "USD" for U.S. dollars, or a currency symbol, e.g. "$".
	 *                           In the latter case results will include any event with the matching currency symbol,
	 *                           this might lead to ambiguous results.
	 *
	 * @return array An array of query arguments that will be added to the main query.
	 */
	public function filter_by_cost_greater_than( $value, $symbol = null ) {
		return $this->by( 'cost', $value, '>', $symbol );
	}

	/**
	 * Filters events that have a cost less than the given value.
	 *
	 * Cost search is NOT inclusive.
	 *
	 * @since TBD
	 *
	 * @param float  $value      The cost to use for the comparison.
	 * @param string $symbol     The desired currency symbol or symbols; this symbol can be a currency ISO code,
	 *                           e.g. "USD" for U.S. dollars, or a currency symbol, e.g. "$".
	 *                           In the latter case results will include any event with the matching currency symbol,
	 *                           this might lead to ambiguous results.
	 *
	 * @return array An array of query arguments that will be added to the main query.
	 */
	public function filter_by_cost_less_than( $value, $symbol = null ) {
		return $this->by( 'cost', $value, '<', $symbol );
	}
}
