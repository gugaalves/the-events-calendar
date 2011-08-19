<?php
/**
* Widget admin for the event list widget.
*/

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

?>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:',self::PLUGIN_DOMAIN);?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e('Show:',self::PLUGIN_DOMAIN);?></label>
	<select id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" class="widefat">
	<?php for ($i=1; $i<=10; $i++)
	{?>
	<option <?php if ( $i == $instance['limit'] ) {echo 'selected="selected"';}?> > <?php echo $i;?> </option>
	<?php } ?>							
	</select>
</p>
	<label for="<?php echo $this->get_field_id( 'no_upcoming_events' ); ?>"><?php _e('Show widget only if there are upcoming events:',self::PLUGIN_DOMAIN);?></label>
	<input id="<?php echo $this->get_field_id( 'no_upcoming_events' ); ?>" name="<?php echo $this->get_field_name( 'no_upcoming_events' ); ?>" type="checkbox" <?php checked( $instance['no_upcoming_events'], 1 ); ?> value="1" />
<p> 

</p>

<p><?php _e( 'Display:', self::PLUGIN_DOMAIN ); ?><br />

<?php $displayoptions = array (
					"start" => __('Start Date & Time', self::PLUGIN_DOMAIN) .'<small><br/>'.__('(Widget will always show start date)', self::PLUGIN_DOMAIN).'</small>',
					"end" => __("End Date & Time", self::PLUGIN_DOMAIN),
					"venue" => __("Venue", self::PLUGIN_DOMAIN),
					"address" => __("Address", self::PLUGIN_DOMAIN),
					"city" => __("City", self::PLUGIN_DOMAIN),
					"region" => __("State (US) Or Province (Int)", self::PLUGIN_DOMAIN),
					"zip" => __("Postal Code", self::PLUGIN_DOMAIN),
					"country" => __("Country", self::PLUGIN_DOMAIN),
					"phone" => __("Phone", self::PLUGIN_DOMAIN),
					"cost" => __("Price", self::PLUGIN_DOMAIN),
				);
	foreach ($displayoptions as $option => $label) { ?>
		<input class="checkbox" type="checkbox" <?php checked( $instance[$option], true ); ?> id="<?php echo $this->get_field_id( $option ); ?>" name="<?php echo $this->get_field_name( $option ); ?>" style="margin-left:5px"/>
		<label for="<?php echo $this->get_field_id( $option ); ?>"><?php echo $label ?></label>
		<br/>
<?php } ?>
	<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e('Category:',self::PLUGIN_DOMAIN);?>
		<?php 

			echo wp_dropdown_categories( array(
				'show_option_none' => 'All Events',
				'hide_empty' => 0,
				'echo' => 0,
				'name' => $this->get_field_name( 'category' ),
				'id' => $this->get_field_id( 'category' ),
				'taxonomy' => 'sp_events_cat',
				'selected' => $instance['category']
			));
		?>
</p>
<p><small><em><?php _e('If you wish to customize the widget display yourself, see the file views/events-list-load-widget-display.php inside the Events Premium plugin.', self::PLUGIN_DOMAIN);?></em></small></p>