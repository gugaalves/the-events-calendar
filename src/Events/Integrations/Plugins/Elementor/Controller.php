<?php
/**
 * Controller for Events Calendar Pro Elementor integrations.
 *
 * @since   TBD
 *
 * @package TEC\Events\Integrations\Plugins\Elementor
 */

namespace TEC\Events\Integrations\Plugins\Elementor;

use TEC\Common\Integrations\Traits\Plugin_Integration;
use TEC\Events\Integrations\Integration_Abstract;
use TEC\Events\Integrations\Plugins\Elementor\Template\Controller as Template_Controller;
use TEC\Events\Custom_Tables\V1\Models\Occurrence;

/**
 * Class Controller
 *
 * @since   TBD
 *
 * @package TEC\Events\Integrations\Plugins\Elementor
 */
class Controller extends Integration_Abstract {
	use Plugin_Integration;

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 */
	public static function get_slug(): string {
		return 'elementor';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 *
	 * @return bool Whether or not integrations should load.
	 */
	public function load_conditionals(): bool {
		return ! empty( ELEMENTOR_PATH );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since TBD
	 */
	public function load(): void {
		$this->container->register_on_action( 'elementor/init', Template_Controller::class );
		$this->container->register_on_action( 'elementor/loaded', Assets_Manager::class );
		$this->container->register_on_action( 'elementor/widgets/register', Widgets_Manager::class );

		$this->register_actions();
		$this->register_filters();

		// Make sure we instantiate the assets manager.
		tribe( Assets_Manager::class );

		// Make sure we instantiate the templates controller.
		tribe( Template_Controller::class );
	}

	/**
	 * Register actions.
	 *
	 * @since TBD
	 */
	public function register_actions(): void {
		add_action( 'elementor/document/after_save', [ $this, 'action_elementor_document_after_save' ], 10, 2 );
	}

	/**
	 * Register filters.
	 *
	 * @since TBD
	 */
	public function register_filters(): void {}

	/**
	 * Checks if Elementor Pro is active.
	 * For registering controllers, etc, use register_on_action(  'elementor_pro/init' )
	 *
	 * @since TBD
	 *
	 * @return bool
	 */
	public function is_elementor_pro_active(): bool {
		return defined( 'ELEMENTOR_PRO_VERSION' );
	}

	/**
	 * Test function to re-save the metadata as the base post in a series.
	 *
	 * This is a temporary solution to fix the issue with the Elementor data not being saved on the real post.
	 * It's NOT WORKING CORRECTLY as of yet, and the issue is still being investigated.
	 *
	 * @since TBD
	 *
	 * @param \Elementor\Core\DocumentTypes\Post $document The document.
	 * @param array                              $editor_data The editor data.
	 */
	public function action_elementor_document_after_save( $document, $editor_data ): void {
		if ( empty( $document ) ) {
			return;
		}

		$occurrence_id = $document->get_main_id();
		$event         = tribe_get_event( $occurrence_id );

		// This is an occurrence the real post ID is hold as a reference on the occurrence table.
		if ( empty( $event->_tec_occurrence->post_id ) || ! $event->_tec_occurrence instanceof Occurrence ) {
			return;
		}

		$saved_meta = get_post_meta( $occurrence_id, '_elementor_data', true );

		$real_id = $event->_tec_occurrence->post_id;

		// Don't use `update_post_meta` that can't handle `revision` post type.
		$is_meta_updated = update_metadata( 'post', $real_id, '_elementor_data', $saved_meta );
	}
}
