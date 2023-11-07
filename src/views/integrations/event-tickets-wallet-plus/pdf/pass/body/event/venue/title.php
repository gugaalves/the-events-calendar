<?php
/**
 * PDF Pass: Body - Venue Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/integrations/event-tickets-wallet-plus/pdf/pass/body/event/venue/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1amp
 *
 * @since TBD
 *
 * @version TBD
 */

?>
<div class="tec-tickets__wallet-plus-pdf-event-venue-title">
	<?php echo esc_html( $venue->post_title ); ?>
</div>