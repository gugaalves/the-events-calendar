<?php
/**
 * View: Elementor Event Organizer widget header.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-organizer/details/phone/content.php
 *
 * @since TBD
 *
 * @var array  $organizer The organizer ID.
 * @var array  $settings  The widget settings.
 * @var int    $event_id  The event ID.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Event_Organizer $widget The widget instance.
 */

?>
<p <?php tribe_classes( $widget->get_phone_base_class() ); ?>><?php echo esc_html( tribe_get_organizer_phone( $organizer ) ); ?></p>
