<?php
		
	/* 
	* 	Helper file to migrate our options from previous version to the proper WordPress settings API
	*	Helper File , called inside class-yikes-inc-easy-mailchimp-extender-admin.php ( migrate_old_yks_mc_options() )
	*	@since v5.4
	* 	@Author: Yikes Inc. 
	*	@Contact: http://www.yikesinc.com/
	*/
		
	// enqueue the styles for our migration page..
	wp_enqueue_style( 'yikes_mc_migrate_option_styles' , YIKES_MC_URL . 'admin/css/yikes-inc-easy-mailchimp-migrate-option-styles.css' );
	wp_enqueue_style( 'animate-css' , YIKES_MC_URL . 'admin/css/animate.min.css' );
	
	// store our old options
	$old_plugin_options = get_option( 'ykseme_storage' );
	
	$global_error_messages = array(
		'success' => __( $old_plugin_options['single-optin-message'] , 'yikes-inc-easy-mailchimp-extender' ),
		'general-error' => __( "Whoops! It looks like something went wrong. Please try again." , 'yikes-inc-easy-mailchimp-extender' ),
		'invalid-email' => __( "Please provide a valid email address." , 'yikes-inc-easy-mailchimp-extender' ),
		'email-exists-error' => __( "The provided email is already subscribed to this list." , 'yikes-inc-easy-mailchimp-extender' )
	);
	
	// if old options are defined...
	if( $old_plugin_options ) {
		
		// Verify the NONCE is valid
		check_admin_referer( 'yikes-mc-migrate-options' , 'migrate_options_nonce' );
		
		?>
			
		<div class="wrap">
			<h3><?php _e( 'Migrating old plugin options' , 'yikes-inc-easy-mailchimp-extender' ); ?><span class="upgrading-ellipse-one">.</span><span class="upgrading-ellipse-two">.</span><span class="upgrading-ellipse-three">.</h3>
			<p><?php _e( 'please be patient while your options are updated and the process has completed' , 'yikes-inc-easy-mailchimp-extender' ); ?></p>
			<!-- empty list, populate when options  get updated -->
			<ul id="options-updated" class="yikes-easy-mc-hidden">
				<hr />
			</ul>
		</div>
				
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				<?php
				
					// loop over our old options, and store them in a new option value
					$do_not_migrate_options = array(
						'ssl_verify_peer', 'api_validation' , 'widget_yikes_mc_widget' , 'flavor' , 'single-optin-message' , 'double-optin-message' ,
						'mailchimp-optIn-default-list' , 'version' , 'yks-mailchimp-jquery-datepicker' , 'ssl_verify_peer' , 'optIn-checkbox' , 'yks-mailchimp-optin-checkbox-text',
						'yks-mailchimp-required-text' , 'optin'
					);
							
					foreach( $old_plugin_options as $option_name => $option_value ) {
						if( ! in_array( $option_name , $do_not_migrate_options ) ) {
							// ajax request to update our options one by one..
							// if its an array, we need to json encode it
							if( is_array( $option_value ) ) {
								if( $option_name == 'lists' ) {
									if( ! empty( $option_value ) ) {
										// update and pass our placeholder value
										reset( $option_value );
										$first_key = key( $option_value );
										$fields = $option_value[$first_key]['fields'];	
										reset( $fields );
										$first_field_key = key( $fields );
										$array_keys = array_keys( $fields );
										$x = 1;								
										foreach( $array_keys as $parent_key ) {
											// update our placeholder key to be 'placeholder'
											$option_value[$first_key]['fields'][$parent_key]['placeholder'] = isset( $option_value[$first_key]['fields'][$parent_key]['placeholder-'.$first_key.'-'.$x] ) ? $option_value[$first_key]['fields'][$parent_key]['placeholder-'.$first_key.'-'.$x] : '';
											// update field classes
											$option_value[$first_key]['fields'][$parent_key]['additional-classes'] = isset( $option_value[$first_key]['fields'][$parent_key]['custom-field-class-'.$first_key.'-'.$x] ) ? $option_value[$first_key]['fields'][$parent_key]['custom-field-class-'.$first_key.'-'.$x] : '';
											// update help field - populate description
											$option_value[$first_key]['fields'][$parent_key]['description'] = isset( $option_value[$first_key]['fields'][$parent_key]['help'] ) ? $option_value[$first_key]['fields'][$parent_key]['help'] : ''; 
											// remove the old placeholder structure
											unset( $option_value[$first_key]['fields'][$parent_key]['placeholder-'.$first_key.'-'.$x] );
											// remove old custom class structure
											unset( $option_value[$first_key]['fields'][$parent_key]['custom-field-class-'.$first_key.'-'.$x] );
											// remove old help/description 
											unset( $option_value[$first_key]['fields'][$parent_key]['help'] );
											$x++;
										}
									} else {
										$option_value = array();
									}
								}
								$option_value = json_encode( $option_value ); 
							}
							/* Rename our ReCaptcha Options */
								/* Public Site Key */
								if( $option_name == 'recaptcha-api-key' ) {
									$option_name = 'recaptcha-site-key';
								}
								/* Private Key */
								if( $option_name == 'recaptcha-private-api-key'  ) {
									$option_name = 'recaptcha-secret-key';
								}
								/* Status */
								/* Change 'recaptcha-setting' to 'recaptcha-status' */
								if( $option_name == 'recaptcha-setting' ) {
									$option_name = 'recaptcha-status';
								}
							/* End  re-name ReCaptcha options */
							?>
								var data = {
									'action': 'migrate_old_plugin_settings',
									'option_name': '<?php echo $option_name; ?>',
									'option_value': '<?php echo $option_value; ?>'
								};
														
								$.post( ajaxurl, data, function(response) {
									jQuery( '#options-updated' ).show();
									jQuery( '#options-updated' ).append( '<li class="animated fadeInDown"><?php echo '<strong>' . ucwords( str_replace( '_' , ' ' , str_replace( '-' , ' ' , $option_name ) ) ) . '</strong> ' . __( "successfully imported." , 'yikes-inc-easy-mailchimp-extender' ); ?></li>' );	
									// count the length of our settings array,
									// once we hit 7, lets redirectem
									if( jQuery( '#options-updated' ).children( 'li' ).length == 7 ) {
										// finished with the loop...lets let the user know....and then redirect them....
										jQuery( '.wrap' ).find( 'h3' ).text( 'Optons Successfuly Imported' );
										jQuery( '.upgrading-ellipse-one' ).remove();
										jQuery( '.upgrading-ellipse-two' ).remove();
										jQuery( '.upgrading-ellipse-three' ).remove();
										jQuery( '.wrap' ).find( 'h3' ).next().fadeOut();
										jQuery( '#options-updated' ).append( '<li class="animated fadeInDown migration-complete-notification"><em><?php _e( "Migration Complete. Please wait..." , 'yikes-inc-easy-mailchimp-extender' ); ?> </em> <img src="<?php echo esc_url_raw( admin_url( "images/wpspin_light.gif" ) ); ?>" /></li>' );
										// redirect our user to the main plugin page...
										setTimeout( function() {
											<?php 
												// migrate options that didnt make it (they were never stored in the 'ykseme_storage' options array)
												add_option( 'yikes-mc-api-validation' , get_option( 'api_validation' , 'invalid_api_key' ) );
												add_option( 'yikes-mc-error-messages' , $global_error_messages );
												// delete our old options after a successful migration (and some new ones that are no longer needed)
												delete_option( 'widget_yikes_mc_widget' );
												delete_option( 'api_validation' );
												delete_option( 'ykseme_storage' );
												delete_option( 'yikes-mc-lists' );
											?>
											window.location.replace( "<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ); ?>" );
										}, 2000);
									}
								});
						<?php
						}
					}
				?>		
			});
		</script>
			
		<?php
		// delete the options after the import, as we no longer need them
		// delete_option( 'ykseme_storage' );
		// else, die and redirect the user to the main admin page
	} else {
		?>
		<div class="wrap">
			<script>
					setTimeout( function() {
						window.location.replace( "<?php echo esc_url_raw( admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ); ?>" );
					}, 2000 );
			</script>
		<?php
			wp_die( '<strong>' . __( 'Old plugin options do not exist. Redirecting you...' , 'yikes-inc-easy-mailchimp-extender' ) . '</strong>' , 500 );
		?>
		</div>
		<?php
	}