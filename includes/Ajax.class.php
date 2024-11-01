<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmAJAX' ) ) {
	/**
	 * Class to handle AJAX interactions for Ultimate WP Mail
	 *
	 * @since 1.0.0
	 */
	class ewduwpmAJAX {

		public function __construct() { 

			add_action( 'wp_ajax_ewd_uwpm_ajax_preview_email', array( $this, 'get_email_preview' ) );

			add_action( 'wp_ajax_ewd_uwpm_interests_sign_up', array( $this, 'save_user_interests' ) );

			add_action( 'wp_ajax_ewd_uwpm_get_email_log_details', array( $this, 'get_email_log_details' ) );

			// Admin Email Actions
			add_action( 'wp_ajax_ewd_uwpm_send_test_email', array( $this, 'send_test_email' ) );
			add_action( 'wp_ajax_ewd_uwpm_email_all_users', array( $this, 'email_all_users' ) );
			add_action( 'wp_ajax_ewd_uwpm_email_user_list', array( $this, 'email_user_list' ) );
			add_action( 'wp_ajax_ewd_uwpm_email_specific_user', array( $this, 'email_specific_user' ) );

			// Admin SMS Actions
			add_action( 'wp_ajax_ewd_uwpm_send_test_sms', array( $this, 'send_test_sms' ) );
			add_action( 'wp_ajax_ewd_uwpm_sms_all_users', array( $this, 'sms_all_users' ) );
			add_action( 'wp_ajax_ewd_uwpm_sms_user_list', array( $this, 'sms_user_list' ) );
			add_action( 'wp_ajax_ewd_uwpm_sms_specific_user', array( $this, 'sms_specific_user' ) );
		}

		/**
		 * Returns the output for a single email
		 * @since 1.0.0
		 */
		public function get_email_preview() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$email_content = $_POST['email_content'];

			$args = array(
				'email_id'	=> intval( $_POST['email_id'] )
			);
		
			$email_content = $ewd_uwpm_controller->notifications->process_email_content( $email_content, $args, wp_get_current_user() );
		
			echo $email_content;
		
			die();
		}
		
		/**
		 * Sends a test email to a specified email address
		 * @since 1.0.0
		 */
		public function send_test_email() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$email_address = sanitize_email( $_POST['email_address'] );
			$email_title = sanitize_text_field( $_POST['email_title'] );
			$email_content = $_POST['email_content'];
			
			$args = array(
				'email_address'	=> empty( $_POST['email_address'] ) ? '' : sanitize_email( $_POST['email_address'] ),
				'email_title'	=> empty( $_POST['email_title'] ) ? '' : sanitize_text_field( $_POST['email_title'] ),
				'email_content'	=> empty( $_POST['email_content'] ) ? '' : $_POST['email_content'],
				'email_id'		=> intval( $_POST['email_id'] )
			);
			
			$success = $ewd_uwpm_controller->notifications->send_email( $args );

			echo $success ? __( 'Email successfully sent to ', 'ultimate-wp-mail') . $email_address : __( 'Email failed to send', 'ultimate-wp-mail' );
		
			die();
		}
		
		/**
		 * Sends an email to all of the users of the website
		 * @since 1.0.0
		 */
		public function email_all_users() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$params = array(
				'email_id' 		=> empty( $_POST['email_id'] ) ? 0 : intval( $_POST['email_id'] ),
				'email_title' 	=> empty( $_POST['email_title'] ) ? 0 : sanitize_text_field( $_POST['email_title'] ),
				'email_content' => empty( $_POST['email_content'] ) ? 0 : $_POST['email_content'],
				'send_time' 	=> empty( $_POST['send_time'] ) ? 'now' : sanitize_text_field( $_POST['send_time'] ),
				'send_type'		=> 'all'
			);
		
			if ( $params['send_time'] == 'now' ) { echo $ewd_uwpm_controller->notifications->email_all_users( $params ); }
			else { $ewd_uwpm_controller->notifications->schedule_email_send( $params ); }
		
			die();
		}
		
		/**
		 * Sends an email to a specific list of users, either auto-generated or created via the admin page 
		 * @since 1.0.0
		 */
		public function email_user_list() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$params = array(
				'email_id' 		=> empty( $_POST['email_id'] ) ? 0 : intval( $_POST['email_id'] ),
				'email_title' 	=> empty( $_POST['email_title'] ) ? 0 : sanitize_text_field( $_POST['email_title'] ),
				'email_content' => empty( $_POST['email_content'] ) ? '' : $_POST['email_content'],
				'list_id' 		=> empty( $_POST['list_id'] ) ? 0 : intval( $_POST['list_id'] ),
				'send_time' 	=> empty( $_POST['send_time'] ) ? 'now' : sanitize_text_field( $_POST['send_time'] ),
				'send_type'		=> 'list',
				'interests'		=> array(
					'post_categories' 	=> empty( $_POST['post_categories'] ) ? array() : explode( ',', $_POST['post_categories'] ),
					'uwpm_categories' 	=> empty( $_POST['uwpm_categories'] ) ? array() : explode( ',', $_POST['uwpm_categories'] ),
					'wc_categories' 	=> empty( $_POST['wc_categories'] ) ? array() : explode( ',', $_POST['wc_categories'] ),
				),
				'woocommerce'	=> array(
					'previous_purchasers' 		=> empty( $_POST['previous_purchasers'] ) ? '' : sanitize_text_field( $_POST['previous_purchasers'] ),
					'product_purchasers' 		=> empty( $_POST['product_purchasers'] ) ? '' : sanitize_text_field( $_POST['product_purchasers'] ),
					'previous_wc_products' 		=> empty( $_POST['previous_wc_products'] ) ? '' : sanitize_text_field( $_POST['previous_wc_products'] ),
					'category_purchasers' 		=> empty( $_POST['category_purchasers'] ) ? '' : sanitize_text_field( $_POST['category_purchasers'] ),
					'previous_wc_categories' 	=> empty( $_POST['previous_wc_categories'] ) ? '' : sanitize_text_field( $_POST['previous_wc_categories'] ),
				)
			);
		
			if ( $params['send_time'] == 'now' ) {echo $ewd_uwpm_controller->notifications->email_user_list( $params );}
			else { $ewd_uwpm_controller->notifications->schedule_email_send( $params ); }
		
			die();
		}
		
		/**
		 * Sends an email to a specific user
		 * @since 1.0.0
		 */
		public function email_specific_user() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$params = array(
				'email_id' 		=> empty( $_POST['email_id'] ) ? 0 : intval( $_POST['email_id'] ),
				'email_title' 	=> empty( $_POST['email_title'] ) ? 0 : sanitize_text_field( $_POST['email_title'] ),
				'email_content' => empty( $_POST['email_content'] ) ? 0 : $_POST['email_content'],
				'user_id' 		=> empty( $_POST['user_id'] ) ? 0 : intval( $_POST['user_id'] ),
				'send_time' 	=> empty( $_POST['send_time'] ) ? 'now' : sanitize_text_field( $_POST['send_time'] ),
				'send_type'		=> 'user'
			);
		
			if ( $params['send_time'] == 'now' ) { echo $ewd_uwpm_controller->notifications->email_user( $params ); }
			else { $ewd_uwpm_controller->notifications->schedule_email_send( $params ); }
		
			die();
		}

		/**
		 * Sends a test sms to a specified phone number
		 * @since 1.2.0
		 */
		public function send_test_sms() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}
			
			$args = array(
				'phone_number'	=> empty( $_POST['phone_number'] ) ? '' : sanitize_text_field( $_POST['phone_number'] ),
				'email_content'	=> empty( $_POST['message_content'] ) ? '' : sanitize_text_field( $_POST['message_content'] ),
				'email_id'		=> empty( $_POST['message_id'] ) ? 0 : intval( $_POST['message_id'] )
			);
			
			$success = $ewd_uwpm_controller->notifications->send_sms( $args );

			echo $success ? __( 'SMS successfully sent to ', 'ultimate-wp-mail') . $args['phone_number'] : __( 'SMS failed to send', 'ultimate-wp-mail' );
		
			die();
		}
		
		/**
		 * Sends an sms to all of the users of the website
		 * @since 1.2.0
		 */
		public function sms_all_users() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$params = array(
				'email_id' 		=> empty( $_POST['message_id'] ) ? 0 : intval( $_POST['message_id'] ),
				'email_content' => empty( $_POST['message_content'] ) ? 0 : sanitize_text_field( $_POST['message_content'] ),
				'send_time' 	=> empty( $_POST['send_time'] ) ? 'now' : sanitize_text_field( $_POST['send_time'] ),
				'send_type'		=> 'all'
			);
		
			if ( $params['send_time'] == 'now' ) { echo $ewd_uwpm_controller->notifications->sms_all_users( $params ); }
			else { $ewd_uwpm_controller->notifications->schedule_email_send( $params ); }
		
			die();
		}
		
		/**
		 * Sends an sms to a specific list of users, either auto-generated or created via the admin page 
		 * @since 1.2.0
		 */
		public function sms_user_list() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$params = array(
				'email_id' 		=> empty( $_POST['message_id'] ) ? 0 : intval( $_POST['message_id'] ),
				'email_content' => empty( $_POST['message_content'] ) ? '' : sanitize_text_field( $_POST['message_content'] ),
				'list_id' 		=> empty( $_POST['list_id'] ) ? 0 : intval( $_POST['list_id'] ),
				'send_time' 	=> empty( $_POST['send_time'] ) ? 'now' : sanitize_text_field( $_POST['send_time'] ),
				'send_type'		=> 'list',
				'interests'		=> array(
					'post_categories' 	=> empty( $_POST['post_categories'] ) ? array() : explode( ',', $_POST['post_categories'] ),
					'uwpm_categories' 	=> empty( $_POST['uwpm_categories'] ) ? array() : explode( ',', $_POST['uwpm_categories'] ),
					'wc_categories' 	=> empty( $_POST['wc_categories'] ) ? array() : explode( ',', $_POST['wc_categories'] ),
				),
				'woocommerce'	=> array(
					'previous_purchasers' 		=> empty( $_POST['previous_purchasers'] ) ? '' : sanitize_text_field( $_POST['previous_purchasers'] ),
					'product_purchasers' 		=> empty( $_POST['product_purchasers'] ) ? '' : sanitize_text_field( $_POST['product_purchasers'] ),
					'previous_wc_products' 		=> empty( $_POST['previous_wc_products'] ) ? '' : sanitize_text_field( $_POST['previous_wc_products'] ),
					'category_purchasers' 		=> empty( $_POST['category_purchasers'] ) ? '' : sanitize_text_field( $_POST['category_purchasers'] ),
					'previous_wc_categories' 	=> empty( $_POST['previous_wc_categories'] ) ? '' : sanitize_text_field( $_POST['previous_wc_categories'] ),
				)
			);
		
			if ( $params['send_time'] == 'now' ) {echo $ewd_uwpm_controller->notifications->sms_user_list( $params );}
			else { $ewd_uwpm_controller->notifications->schedule_email_send( $params ); }
		
			die();
		}
		
		/**
		 * Sends an sms to a specific user
		 * @since 1.2.0
		 */
		public function sms_specific_user() {
			global $ewd_uwpm_controller;

			// Authenticate request
			if ( ! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) ) {
			
				ewduwpmHelper::admin_nopriv_ajax();
			}

			$params = array(
				'email_id' 		=> empty( $_POST['message_id'] ) ? 0 : intval( $_POST['message_id'] ),
				'email_content' => empty( $_POST['message_content'] ) ? 0 : $_POST['message_content'],
				'user_id' 		=> empty( $_POST['user_id'] ) ? 0 : intval( $_POST['user_id'] ),
				'send_time' 	=> empty( $_POST['send_time'] ) ? 'now' : sanitize_text_field( $_POST['send_time'] ),
				'send_type'		=> 'user'
			);
		
			if ( $params['send_time'] == 'now' ) { echo $ewd_uwpm_controller->notifications->sms_user( $params ); }
			else { $ewd_uwpm_controller->notifications->schedule_email_send( $params ); }
		
			die();
		}
		
		/**
		 * Saves a user's interest topics so they can be automatically emailed later
		 * @since 1.0.0
		 */
		public function save_user_interests() {

			$post_categories = ! empty( $_POST['post_categories'] ) ? explode( ',', sanitize_text_field( $_POST['post_categories'] ) ) : array();
			$uwpm_categories = ! empty( $_POST['uwpm_categories'] ) ? explode( ',', sanitize_text_field( $_POST['uwpm_categories'] ) ) : array();
			$wc_categories = ! empty( $_POST['wc_categories'] ) ? explode( ',', sanitize_text_field( $_POST['wc_categories'] ) ) : array();
		
			$possible_post_categories = ! empty( $_POST['possible_post_categories'] ) ? explode( ',', sanitize_text_field( $_POST['possible_post_categories'] ) ) : array();
			$possible_uwpm_categories = ! empty( $_POST['possible_uwpm_categories'] ) ? explode( ',', sanitize_text_field( $_POST['possible_uwpm_categories'] ) ) : array();
			$possible_wc_categories = ! empty( $_POST['possible_wc_categories'] ) ? explode( ',', sanitize_text_field( $_POST['possible_wc_categories'] ) ) : array();
		
			$user_id = get_current_user_id();
			
			$current_post_categories = (array) get_user_meta( $user_id, 'EWD_UWPM_Post_Interests', true ); 
			$current_uwpm_categories = (array) get_user_meta( $user_id, 'EWD_UWPM_UWPM_Interests', true );
			$current_wc_categories = (array) get_user_meta( $user_id, 'EWD_UWPM_WC_Interests', true );
			
			$updated_post_categories = array_unique( array_merge( $post_categories, array_diff( $current_post_categories, $possible_post_categories ) ) );
			$updated_uwpm_categories = array_unique( array_merge( $uwpm_categories, array_diff( $current_uwpm_categories, $possible_uwpm_categories ) ) );
			$updated_wc_categories = array_unique( array_merge( $wc_categories, array_diff( $current_wc_categories, $possible_wc_categories ) ) );

			update_user_meta( $user_id, 'EWD_UWPM_Post_Interests', $updated_post_categories );
			update_user_meta( $user_id, 'EWD_UWPM_UWPM_Interests', $updated_uwpm_categories );
			update_user_meta( $user_id, 'EWD_UWPM_WC_Interests', $updated_wc_categories );
	
			return __( 'Interests successfully updated.', 'ultimate-wp-mail' );
		
			die();
		}

		/**
		 * Get the details of an email log entry (message, headers, etc.)
		 * @since 1.1.0
		 */
		public function get_email_log_details() {

			$post_id = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

			$post = get_post( $post_id );

			if ( empty( $post ) ) {

				wp_send_json_error(
					array(
						'error'		=> 'invalid_post_id',
						'msg'		=>  __( 'There is no post that matches the received ID.', 'ultimate-wp-mail' ),
					)
				);
			}

			if ( get_post_type( $post ) != EWD_UWPM_EMAIL_LOG_POST_TYPE ) {

				wp_send_json_error(
					array(
						'error'		=> 'invalid_post_type',
						'msg'		=>  __( 'The received post id is for a post that is not of the email log type.', 'ultimate-wp-mail' ),
					)
				);
			}

			ob_start();

			?>

			<div class="ewd-uwpm-email-log-details-modal">

				<div class="ewd-uwpm-email-log-details-modal-title">
					<?php echo sprintf( __( 'Details for \'%s\' email', 'ultimate-wp-mail' ), esc_html( $post->post_title ) ); ?>
				</div>

				<div class="ewd-uwpm-email-log-details-modal-content">
					<label class="ewd-uwpm-email-log-details-modal-label"><?php _e( 'Status', 'ultimate-wp-mail' ); ?>:</label>
					<span class="ewd-uwpm-email-log-details-modal-value">
						<?php echo esc_html( get_post_meta( $post->ID, 'status', true ) ); ?>
					</span>
				</div>

				<div class="ewd-uwpm-email-log-details-modal-content">
					<label class="ewd-uwpm-email-log-details-modal-label"><?php _e( 'To', 'ultimate-wp-mail' ); ?>:</label>
					<span class="ewd-uwpm-email-log-details-modal-value">
						<?php echo esc_html( get_post_meta( $post->ID, 'recipient', true ) ); ?>
					</span>
				</div>

				<div class="ewd-uwpm-email-log-details-modal-content">
					<label class="ewd-uwpm-email-log-details-modal-label"><?php _e( 'Headers', 'ultimate-wp-mail' ); ?>:</label>
					<span class="ewd-uwpm-email-log-details-modal-value">
						<?php echo esc_html( is_array( get_post_meta( $post->ID, 'headers', true ) ) ? implode( ',', get_post_meta( $post->ID, 'headers', true ) ) : get_post_meta( $post->ID, 'headers', true ) ); ?>
					</span>
				</div>

				<div class="ewd-uwpm-email-log-details-modal-content">
					<label class="ewd-uwpm-email-log-details-modal-label"><?php _e( 'Attachments', 'ultimate-wp-mail' ); ?>:</label>
					<span class="ewd-uwpm-email-log-details-modal-value">
						<?php echo esc_html( is_array( get_post_meta( $post->ID, 'attachments', true ) ) ? implode( ',', get_post_meta( $post->ID, 'attachments', true ) ) : get_post_meta( $post->ID, 'attachments', true ) ); ?>
					</span>
				</div>

				<div class="ewd-uwpm-email-log-details-modal-message">
					<label class="ewd-uwpm-email-log-details-modal-label">Message:</label>
					<div class="ewd-uwpm-email-log-details-modal-the-message">
						<?php echo wp_kses_post( $post->post_content ); ?>
					</div>
				</div>

			</div>

			<div class="ewd-uwpm-email-log-details-modal-background"></div>

			<?php

			$output = ob_get_clean();

			wp_send_json_success(
				array(
					'output'	=> $output,
				)
			);
		}
	}
}