<?php 
/* 
 * By modifying this in your theme folder within plugins/events-manager/templates/events-search.php, you can change the way the search form will look.
 * To ensure compatability, it is recommended you maintain class, id and form name attributes, unless you now what you're doing. 
 * You also must keep the _wpnonce hidden field in this form too.
 */
?>
<div class="em-events-search">
	<?php $s = __('Search Events', 'dbem'); ?>
	<form action="<?php echo EM_URI; ?>" method="post" class="em-events-search-form">
		<?php do_action('em_template_events_search_form_header'); ?>
		<!-- START General Search -->
		<?php /* This general search will find matches within event_name, event_notes, and the location_name, address, town, state and country. */ ?>
		<input type="text" name="search" value="<?php echo $s; ?>" onfocus="if(this.value=='<?php echo $s; ?>')this.value=''" onblur="if(this.value=='')this.value='<?php echo $s; ?>'" />
		<!-- END General Search -->
		<!-- START Category Search -->
		<select name="category">
			<option value=''><?php _e('All Categories','dbem'); ?></option>
			<?php foreach(EM_Categories::get(array('orderby'=>'category_name')) as $EM_Category): ?>
			 <option value="<?php echo $EM_Category->id; ?>" <?php echo ($_POST['category'] == $EM_Category->id) ? 'selected="selected"':''; ?>><?php echo $EM_Category->name; ?></option>
			<?php endforeach; ?>
		</select>
		<!-- END Category Search -->
		<!-- START Country Search -->
		<select name="country">
			<option value=''><?php _e('All Countries','dbem'); ?></option>
			<?php foreach(em_get_countries() as $country_key => $country_name):	?>
			 <option value="<?php echo $country_key; ?>" <?php echo ($_POST['country'] == $country_key) ? 'selected="selected"':''; ?>><?php echo $country_name; ?></option>
			<?php endforeach; ?>
		</select>
		<!-- END Country Search -->		
		<!-- START State/County Search -->
		<select name="state">
			<option value=''><?php _e('All States','dbem'); ?></option>
			<?php 
			//get the counties from locations table
			global $wpdb;
			$states = $wpdb->get_results("SELECT DISTINCT location_state FROM ".EM_LOCATIONS_TABLE." WHERE location_state IS NOT NULL AND location_state != '' ORDER BY location_state ASC", ARRAY_N);
			foreach($states as $state): 
			?>
			 <option value="<?php echo $state[0]; ?>" <?php echo ($_POST['state'] == $state[0]) ? 'selected="selected"':''; ?>><?php echo $state[0]; ?></option>
			<?php endforeach; ?>
		</select>
		<!-- END State/County Search -->	
		<!-- START Region Search -->
		<select name="region">
			<option value=''><?php _e('All Regions','dbem'); ?></option>
			<?php 
			//get the counties from locations table
			global $wpdb;
			$states = $wpdb->get_results("SELECT DISTINCT location_region FROM ".EM_LOCATIONS_TABLE." WHERE location_region IS NOT NULL AND location_region != '' ORDER BY location_region ASC", ARRAY_N);
			foreach($states as $state): 
			?>
			 <option value="<?php echo $state[0]; ?>" <?php echo ($_POST['state'] == $state[0]) ? 'selected="selected"':''; ?>><?php echo $state[0]; ?></option>
			<?php endforeach; ?>
		</select>
		<!-- END State/County Search -->
		<?php do_action('em_template_events_search_form_ddm'); ?>
		<!-- START Date Search -->
		or search between:
		<input type="text" id="em-date-start-loc" />
		<input type="hidden" id="em-date-start" name="scope[0]" value="<?php if( !empty($_POST['scope'][0]) ) echo $_POST['scope'][0]; ?>" />
		and
		<input type="text" id="em-date-end-loc" />
		<input type="hidden" id="em-date-end" name="scope[1]" value="<?php if( !empty($_POST['scope'][1]) ) echo $_POST['scope'][1]; ?>" />
		<!-- END Date Search -->
		<?php do_action('em_template_events_search_form_footer'); ?>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('search_events'); ?>" />
		<input type="submit" value="<?php _e('Search','dbem'); ?>" />		
	</form>
</div>
<script type="text/javascript">
	jQuery(document).ready( function($){
		$('.em-events-search-form select[name=country]').change( function(){
			$('.em-events-search select[name=state]').html('<option><?php _e('Loading...','dbem'); ?></option>');
			$('.em-events-search select[name=region]').html('<option><?php _e('Loading...','dbem'); ?></option>');
			var data = {
				_wpnonce : '<?php echo wp_create_nonce('search_states'); ?>',
				action : 'search_states',
				country : $(this).val(),
				return_html : true
			};
			$('.em-events-search select[name=state]').load( EM.ajaxurl, data );
			data.action = 'search_regions';
			data._wpnonce = '<?php echo wp_create_nonce('search_regions'); ?>';
			$('.em-events-search select[name=region]').load( EM.ajaxurl, data );
		});
		
		//in order for this to work, you need the above classes to be present in your theme
		$('.em-events-search-form').submit(function(){
	    	if( this.search.value=='<?php echo $s; ?>'){
	    		this.search.value = '';
	    	}
	    	if( $('#em-wrapper .em-events-list').length == 1 ){
				$(this).ajaxSubmit({
					url : EM.ajaxurl,
				    data : {
						_wpnonce : '<?php echo wp_create_nonce('search_states'); ?>',
						action : 'search_states',
						country : $(this).val(),
						return_html : true
					},
				    beforeSubmit: function(form) {
						$('.em-events-search-form :submit').val('<?php _e('Searching...','dbem'); ?>');
				    },
				    success : function(responseText) {
						$('.em-events-search-form :submit').val('<?php _e('Search','dbem'); ?>');
						$('#em-wrapper .em-events-list').replaceWith(responseText);
				    }
				});
	    	} 
		});
	});	
</script>