<?php
/**
 * View: Recent Past Event Heading
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/recent-past/heading.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version TBD
 */

$label = sprintf( __( 'Latest Past %s', 'the-events-calendar' ), tribe_get_event_label_plural() );
?>
<h2 class="tribe-events-calendar-recent-past__heading tribe-common-h5 tribe-common-h3--min-medium">
	<?php echo esc_html( $label ); ?>
</h2>
