<?php
/**
 * View: Elementor Event Categories widget header.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events/integrations/elementor/widgets/event-categories/header.php
 *
 * @since TBD
 *
 * @var bool   $show         Whether to show the header.
 * @var string $header_tag   The HTML tag to use for the header.
 * @var string $header_text  The header text.
 * @var array  $settings     The widget settings.
 * @var int    $event_id     The event ID.
 * @var Tribe\Events\Integrations\Elementor\Widgets\Event_Categories $widget The widget instance.
 */

if ( ! $show ) {
	return;
}
?>

<<?php echo tag_escape( $header_tag ); ?> <?php tribe_classes( $widget->get_header_class() ); ?>><?php echo esc_html( $header_text ); ?></<?php echo tag_escape( $header_tag ); ?>>
