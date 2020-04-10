<?php
/**
 * View: Top Bar
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/recent-past/top-bar.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version TBD
 *
 */
?>
<div class="tribe-events-c-top-bar tribe-events-header__top-bar">

	<?php $this->template( 'recent-past/top-bar/nav' ); ?>

	<?php $this->template( 'components/top-bar/today' ); ?>

	<?php $this->template( 'recent-past/top-bar/datepicker' ); ?>

	<?php $this->template( 'components/top-bar/actions' ); ?>

</div>
