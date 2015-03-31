<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$post_type = Tribe__Events__Events::POSTTYPE;
$active = $this->get_active_tab();

?>
<style type="text/css">
	div.tribe_settings {
		width: 90%;
	}
</style>
<div class="tribe_settings wrap">
	<?php screen_icon(); ?><h2><?php _e( 'Events Import', 'tribe-events-calendar' ) ?></h2>

	<h2 class="nav-tab-wrapper">
		<?php
		foreach( $this->get_available_tabs() as $_label => $_tab ){
			if( $_tab == $active ){
				$class = "nav-tab nav-tab-active";
			} else {
				$class = "nav-tab";
			}
			?><a class="<?php echo $class; ?>" href="?post_type=<?php echo $post_type; ?>&amp;page=events-importer&amp;tab=<?php echo $_tab; ?>">
				<?php echo $_label; ?>
			</a><?php
		}
		?>
	</h2>

	<div class="form">
