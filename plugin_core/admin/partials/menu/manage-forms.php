<?php		// Run our custom query to retreive our forms from the table we've created	global $wpdb;		/*********************/	/*	Get all forms	 */	/********************/	// return it as an array, so we can work with it to build our form below	$form_results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'yikes_easy_mc_forms', ARRAY_A );		/* Store Data if User is Authorized */	if( $this->is_user_mc_api_valid_form( false ) == 'valid' ) {		/// Check for a transient, if not - set one up for one hour		if ( false === ( $list_data = get_transient( 'yikes-easy-mailchimp-list-data' ) ) ) {			// initialize MailChimp Class			$MailChimp = new MailChimp( get_option( 'yikes-mc-api-key' , '' ) );			// retreive our list data			$list_data = $MailChimp->call( 'lists/list' , array( 'apikey' => get_option( 'yikes-mc-api-key' , '' ) ) );			// set our transient			set_transient( 'yikes-easy-mailchimp-list-data', $list_data, 1 * HOUR_IN_SECONDS );		}	} else {		$list_data = null;	}?><div class="wrap">	<!-- Freddie Logo -->	<img src="<?php echo YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png'; ?>" alt="Freddie - MailChimp Mascot" style="float:left;margin-right:10px;" />			<h2>Easy MailChimp by Yikes Inc. | <?php _e( 'Manage Forms' , $this->text_domain ) ?></h2>							<!-- Settings Page Description -->	<p class="yikes-easy-mc-about-text about-text"><?php _e( 'Create and manage your MailChimp opt-in forms on the following page. Select a form to make edits to it.' , $this->text_domain ); ?></p>				<?php			// Confirm that the necessary forms table in the database exists		if( $wpdb->get_var("show tables like '" . $wpdb->prefix . "yikes_easy_mc_forms'") != $wpdb->prefix . "yikes_easy_mc_forms" ) {			wp_die( '<div class="error"><p>' . __( 'It looks like the forms table is missing. Please de-activate and re-activate the plugin to attempt to create the table. If the error persists, please get in touch with the Yikes Inc. support team.', $this->text_domain ) . '</p></div>' , 500 );		}			/* If the user hasn't authenticated yet, lets kill off */		if( get_option( 'yikes-mc-api-validation' , 'invalid_api_key' ) != 'valid_api_key' ) {			wp_die( '<div class="error"><p>' . __( 'You need to connect to MailChimp before you can start creating forms. Head over to the' , $this->text_domain ) . ' <a href="' . admin_url( 'admin.php?page=yikes-inc-easy-mailchimp-settings' ) . '" title="' . __( 'Settings Page' , $this->text_domain ) . '">' . __( 'Settings Page' , $this->text_domain ) . '</a> ' . __( 'and enter your API key.' , $this->text_domain ) . '</p></div>' , 500 );		}				/* Display our admin notices here */		// delete form success		if( isset( $_REQUEST['deleted-form'] ) && $_REQUEST['deleted-form'] == 'true' ) {			?>			<div class="updated manage-form-admin-notice">				<p><?php _e( 'MailChimp Form successfully deleted.', $this->text_domain ); ?></p>			</div>			<?php		}		// duplicate form success		if( isset( $_REQUEST['duplicated-form'] ) && $_REQUEST['duplicated-form'] == 'true' ) {			?>			<div class="updated manage-form-admin-notice">				<p><?php _e( 'MailChimp Form successfully cloned.', $this->text_domain ); ?></p>			</div>			<?php		}		// duplicate form success		if( isset( $_REQUEST['duplicated-form'] ) && $_REQUEST['duplicated-form'] == 'false' ) {			?>			<div class="error manage-form-admin-notice">				<p><?php _e( 'There was an error trying to clone your form. Please try again. If this error persists, please contact the Yikes Inc. support team.', $this->text_domain ); ?></p>			</div>			<?php		}	?>		<!-- entire body content -->		<div id="poststuff">				<div id="post-body" class="metabox-holder columns-2">							<!-- main content -->				<div id="post-body-content">										<div class="meta-box-sortables ui-sortable">												<div class="postbox yikes-easy-mc-postbox">																						<table class="wp-list-table widefat fixed posts" cellspacing="0" id="yikes-easy-mc-manage-forms-table">											<!-- TABLE HEAD -->									<thead>										<tr>											<th id="cb" class="manage-column column-cb check-column num" scope="col"><input type="checkbox" /></th>											<th id="columnname" class="manage-column column-columnname num" scope="col" style="width:55px;"><?php _e( 'ID' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Name' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Description' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'List' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname num" scope="col"  style="width:10%;max-width:75px;"><?php _e( 'Impressions' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname num" scope="col"  style="width:10%;max-width:75px;"><?php _e( 'Submissions' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname num" scope="col"  style="width:10%;max-width:75px;"><?php _e( 'Conversion Rate' , $this->text_domain ); ?></th>										</tr>									</thead>									<!-- end header -->																		<!-- FOOTER -->									<tfoot>										<tr>											<th class="manage-column column-cb check-column num" scope="col"><input type="checkbox" /></th>											<th id="columnname" class="manage-column column-columnname num" scope="col" style="width:55px;"><?php _e( 'ID' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Name' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Form Description' , $this->text_domain ); ?></th>											<th class="manage-column column-columnname" scope="col"><?php _e( 'List' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname num" scope="col"  style="width:10%;max-width:75px;"><?php _e( 'Impressions' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname num" scope="col"  style="width:10%;max-width:75px;"><?php _e( 'Submissions' , $this->text_domain ); ?></th>											<th id="columnname" class="manage-column column-columnname num" scope="col"  style="width:10%;max-width:75px;"><?php _e( 'Conversion Rate' , $this->text_domain ); ?></th>										</tr>									</tfoot>									<!-- end footer -->																		<!-- TABLE BODY -->									<tbody>										<?php if( count( $form_results ) > 0 ) { 												$i = 1;												foreach( $form_results as $form ) { 										?>											<tr class="<?php if( $i % 2 == 0 ) { echo 'alternate'; } ?>">												<th class="check-column num" scope="row"><input type="checkbox" /></th>												<td class="column-columnname num"><span class="form-id-container"><?php echo intval( $form['id'] ); ?></span></td>												<td class="column-columnname"><?php echo stripslashes( $form['form_name'] ); ?>													<div class="row-actions">														<span><a href="<?php echo esc_url( add_query_arg( array( 'id' => $form['id'] ) , admin_url( 'admin.php?page=yikes-mailchimp-edit-form' ) ) ); ?>"><?php _e( "Edit" , $this->text_domain ); ?></a> |</span>														<span><a href="<?php echo esc_url( add_query_arg( array( 'action' => 'yikes-easy-mc-duplicate-form', 'mailchimp-form' => $form['id'] , 'nonce' => wp_create_nonce( 'duplicate-mailchimp-form-'.$form['id'] ) ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) ); ?>"><?php _e( "Duplicate" , $this->text_domain ); ?></a> |</span>														<span><a href="#" class="view-yikes-mc-form-shortcode" data-alt-text="<?php _e( 'Stats' , $this->text_domain ); ?>"><?php _e( "Shortcode" , $this->text_domain ); ?></a> |</span>																												<span><a href="<?php echo esc_url( add_query_arg( array( 'action' => 'yikes-easy-mc-delete-form', 'mailchimp-form' => $form['id'] , 'nonce' => wp_create_nonce( 'delete-mailchimp-form-'.$form['id'] ) ) , admin_url( 'admin.php?page=yikes-inc-easy-mailchimp' ) ) ); ?>" class="yikes-delete-mailchimp-form" onclick="return confirm('<?php echo __( 'Are you sure you want to delete the' , $this->text_domain ) . ' ' . stripslashes( $form['form_name'] ) . ' form? ' . __( 'This cannot be undone.' , $this->text_domain ); ?>');"><?php _e( "Delete" , $this->text_domain ); ?></a></span>													</div>												</td>																								<td class="column-columnname"><?php echo isset( $form['form_description'] ) ? $form['form_description'] : ''; ?></td>												<td class="column-columnname"><?php if( isset( $list_data ) && $list_data['total'] > 0 ) { $key = $this->findMCListID( $form['list_id'] , $list_data['data'] ); if( isset( $key ) ) { echo $list_data['data'][$key]['name']; } else { echo '<strong>' . __( 'List Not Found' , $this->text_domain ) . '</strong>'; } } ?></td>																											<td class="column-columnname num stat-container" style="width:10%;max-width:75px;">													<?php														$impressions = number_format( $form['impressions'] );														echo '<span title="' . __( 'Impressions' , $this->text_domain ) . '">' . $impressions . '</span>';													?>												</td>												<td class="column-columnname num stat-container" style="width:10%;max-width:75px;">													<?php														$submissions = number_format( $form['submissions'] );														echo '<span title="' . __( 'Submissions' , $this->text_domain ) . '">' . $submissions . '</span>';													?>												</td>												<td class="column-columnname num stat-container" style="width:10%;max-width:75px;">													<?php														if( $impressions != 0 ) {															$conversion_rate = '%' . ( round( $form['submissions'] / $form['impressions'] , 2 ) * 100 );															if( $conversion_rate >= '%15' ) {																$conversion_color = '#00cc00'; // green (unicorn!)															} else if( $conversion_rate < '%15' && $conversion_rate >= '%10' ) { 																$conversion_color = '#0080FF'; // light blue (good)															} else if( $conversion_rate < '%10' && $conversion_rate >= '%5' ) {																$conversion_color = '#FFFF32'; // yellow (ok)															} else {																$conversion_color = '#FF0000'; // red (no bueno)															}														} else {															$conversion_rate = '%0';															$conversion_color = '#333333';														}														echo '<span style="color:' . $conversion_color . ';" title="' . __( 'Conversion Rate' , $this->text_domain ) . '">' . $conversion_rate . '</span>'; 													?>												</td>												<!-- shortcode -->												<td class="column-columnname shortcode hidden-class" colspan="3">													<input type="text" class="yikes-mc-shortcode-input yikes-mc-shortcode-input-<?php echo $form['id']; ?>" style="width:100%;color:#333;" onclick="this.setSelectionRange(0, this.value.length)" readonly value='[yikes-mailchimp form="<?php echo $form['id']; ?>"]' />												</td>											</tr>										<?php 													$i++;												}											} else { ?>											<tr class="no-items">												<td class="colspanchange" colspan="8" style="padding:25px 0 25px 25px;"><em><?php _e( 'No MailChimp forms found. Use the form to the right to create a new one.' , $this->text_domain ); ?></em></td>											</tr>										<?php } ?>									</tbody>								</table> 								<!-- end table -->																				</div> <!-- .postbox -->											</div> <!-- .meta-box-sortables .ui-sortable -->									</div> <!-- post-body-content -->								<!-- sidebar -->				<div id="postbox-container-1" class="postbox-container">															<div class="meta-box-sortables">												<div class="postbox yikes-easy-mc-postbox">																									<?php 								$this->generate_manage_forms_sidebar( $list_data['data'] ); 							?>																				</div> <!-- .postbox -->												<?php 							// display, show some love container							$this->generate_show_some_love_container(); 						?>											</div> <!-- .meta-box-sortables -->									</div> <!-- #postbox-container-1 .postbox-container -->							</div> <!-- #post-body .metabox-holder .columns-2 -->						<br class="clear">		</div> <!-- #poststuff --></div>