<?php
/**
 * Handles the registration and set up of the filters required to integrate the plugin custom tables in the normal
 * WP_Query flow.
 *
 * @since   TBD
 *
 * @package TEC\Custom_Tables\V1\WP_Query
 */

namespace TEC\Custom_Tables\V1\WP_Query;

use Serializable;
use TEC\Custom_Tables\V1\Service_Providers\Controllable_Service_Provider;
use TEC\Custom_Tables\V1\WP_Query\Monitors\Custom_Tables_Query_Monitor;
use TEC\Custom_Tables\V1\WP_Query\Monitors\WP_Query_Monitor;
use TEC\Custom_Tables\V1\WP_Query\Repository\Custom_Tables_Query_Filters;
use Tribe__Repository as Repository;
use WP_Query;

/**
 * Class Provider
 *
 * @since   TBD
 *
 * @package TEC\Custom_Tables\V1\WP_Query
 */
class Provider extends \tad_DI52_ServiceProvider implements Controllable_Service_Provider, Serializable, Provider_Contract {
	/**
	 * Register the filters and bindings required to integrate the plugin custom tables in the normal
	 * WP_Query flow.
	 *
	 * @since TBD
	 */
	public function register() {
		if ( ! $this->container->isBound( static::class ) ) {
			// Avoid re-bindings on Service Provider control.
			$this->container->singleton( __CLASS__, $this );
			$this->container->singleton( Replace_Results::class, Replace_Results::class );
			$this->container->singleton( WP_Query_Monitor::class, WP_Query_Monitor::class );
			$this->container->singleton( Custom_Tables_Query_Monitor::class, Custom_Tables_Query_Monitor::class );
		}

		if ( ! has_action( 'pre_get_posts', [ $this, 'attach_monitor' ] ) ) {
			add_action( 'pre_get_posts', [ $this, 'attach_monitor' ], 200 );
		}

		if ( ! has_action( 'tribe_repository_events_init', [ $this, 'replace_repository_query_filters' ] ) ) {
			add_action( 'tribe_repository_events_init', [ $this, 'replace_repository_query_filters' ] );
		}

		wp_cache_add_non_persistent_groups( [ 'tec_occurrences' ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function unregister() {
		remove_action( 'pre_get_posts', [ $this, 'attach_monitor' ], 200 );
		remove_action( 'tribe_repository_events_init', [ $this, 'replace_repository_query_filters' ] );

		$this->container->make( WP_Query_Monitor::class )->detach();
		$this->register = false;
	}

	/**
	 * Attaches a Monitor instance to the running query.
	 *
	 * @since TBD
	 *
	 * @param  WP_Query  $query  A reference to the currently running query.
	 */
	public function attach_monitor( $query ) {
		if ( ! $query instanceof WP_Query ) {
			return;
		}

		$this->container->make( WP_Query_Monitor::class )->attach( $query );
	}

	/**
	 * Hooks into the Event Repository initialization to replace the default Query Filters
	 * with an implementation that will redirect to the custom tables.
	 *
	 * @since TBD
	 *
	 * @param  Repository  $repository  A reference to the instance of the repository that is initializing.
	 */
	public function replace_repository_query_filters( Repository $repository ) {
		$custom_tables_query_filters = $this->container->make( Custom_Tables_Query_Filters::class );
		add_filter( 'posts_groupby', [ $custom_tables_query_filters, 'group_by_occurrence_id' ], 200, 2 );
		$repository->filter_query = $custom_tables_query_filters;
	}

	/**
	 * Implements the method that is going to be invoked to serialize
	 * the class to make sure the Container instance, that uses non-serializable
	 * Closures, will not be part of the serialized data.
	 *
	 * @since TBD
	 *
	 * @return string An empty string, to not serialize the object.
	 */
	public function serialize() {
		return '';
	}

	/**
	 * Returns void to not spawn the object from serialized data.
	 *
	 * @since TBD
	 *
	 * @param string $data The dat
	 *
	 * @return void Return void to not spawn the object from serialized data.
	 */
	public function unserialize( $data ) {
		return;
	}
}