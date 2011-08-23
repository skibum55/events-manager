<?php
/**
 * bp_em_screen_one()
 *
 * Sets up and displays the screen output for the sub nav item "em/screen-one"
 */
function bp_em_events() {
	global $bp, $EM_Notices;
	
	if( bp_is_my_profile() ){
		$EM_Notices->add_info( __('You are currently viewing your public page, this is what other users will see.', 'dbem') );
	}

	/* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_em_events' );

	add_action( 'bp_template_title', 'bp_em_events_title' );
	add_action( 'bp_template_content', 'bp_em_events_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	//bp_core_load_template( apply_filters( 'bp_em_template_screen_one', 'em/screen-one' ) );
}
	/***
	 * The second argument of each of the above add_action() calls is a function that will
	 * display the corresponding information. The functions are presented below:
	 */
	function bp_em_events_title() {
		_e( 'Events', 'dbem' );
	}

	function bp_em_events_content() {
		global $bp, $EM_Notices;
		echo $EM_Notices;
		if( user_can($bp->displayed_user->id,'edit_events') ){
			?>
			<h4><?php _e('My Events', 'dbem'); ?></h4>
			<?php
			$events = EM_Events::get(array('owner'=>$bp->displayed_user->id));
			if( count($events) > 0 ){
				$args = array(
					'format_header' => get_option('dbem_bp_events_list_format_header'),
					'format' => get_option('dbem_bp_events_list_format'),
					'format_footer' => get_option('dbem_bp_events_list_format_footer'),
					'owner' => $bp->displayed_user->id
				);
				echo EM_Events::output($events, $args);
			}else{
				?>
				<p><?php _e('No Events', 'dbem'); ?>. <a href="<?php echo $bp->events->link . 'my-events/edit/'; ?>"><?php _e('Add Event','dbem'); ?></a></p>
				<?php
			}
		}
		?>
		<h4><?php _e("Events I'm Attending", 'dbem'); ?></h4>
		<?php
		$EM_Person = new EM_Person( $bp->displayed_user->id );
		$EM_Bookings = $EM_Person->get_bookings();
		if(count($EM_Bookings->bookings) > 0){
			//Get events here in one query to speed things up
			$event_ids = array();
			foreach($EM_Bookings as $EM_Booking){
				$event_ids[] = $EM_Booking->event_id;
			}
			echo EM_Events::output(array('event'=>$event_ids));
		}else{
			?>
			<p><?php _e('Not attending any events yet.','dbem'); ?></p>
			<?php
		}
	}