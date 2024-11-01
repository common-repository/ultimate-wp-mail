<?php

/**
 * Class to handle sending notifications when an order is submitted or updated
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduwpmNotifications' ) ) {
class ewduwpmNotifications {

	// Hold all parameters passed to the notification
	public $params = array();

	public function __construct() {

		add_action( 'init', 								array( $this, 'run_delayed_send_on_emails' ) );
		
		// User Events
		add_action( 'user_register', 						array( $this, 'send_on_user_registers' ) );
		add_action( 'personal_options_update', 				array( $this, 'send_on_user_profile_updated' ) );
		add_action( 'set_user_role', 						array( $this, 'send_on_user_role_changed' ) );

		add_filter( 'password_change_email', 				array( $this, 'send_on_user_password_reset' ), 10, 3 );

		// Post Events
		add_action( 'publish_post', 						array( $this, 'send_on_post_published' ) );
		add_action( 'publish_post', 						array( $this, 'save_post_to_send_on_post_published_interest' ) );
		add_action( 'init', 								array( $this, 'send_on_post_published_interest' ) );
		add_action( 'comment_post', 						array( $this, 'send_on_new_comment_on_post' ), 10, 2);

		//CPT Events
		add_action( 'publish_post', 						array( $this, 'cpt_send_on_post_action' ) );
		add_action( 'post_updated', 						array( $this, 'cpt_send_on_post_action' ) );
		add_action( 'before_delete_post', 					array( $this, 'cpt_send_on_post_action' ) );

		// WC Events
		add_action( 'publish_product', 						array( $this, 'send_on_new_product_added' ) );
		add_action( 'woocommerce_payment_complete', 		array( $this, 'send_on_product_purchased' ) );
		add_action( 'woocommerce_checkout_subscription_created', array( $this, 'send_on_subscription_created' ) );

		add_action( 'phpmailer_init', 						array( $this, 'phpmailer_init' ) );
		add_action( 'ewd_uwpm_headers', 					array( $this, 'amend_headers' ) );
		add_filter( 'wp_mail_from', 						array( $this, 'filter_mail_from_email' ), PHP_INT_MAX );

		add_action( 'wp_mail_failed',     					array( $this, 'maybe_notify_wp_mail_error' ) );
	}

	public function run_delayed_send_on_emails() {
		global $ewd_uwpm_controller;

		if ( get_transient( 'ewd-uwpm-send-on-cache' ) ) { return; }

		$delay = intval($ewd_uwpm_controller->settings->get_setting('schedule-check-delay'));

		if(1 > $delay) {
			$delay = 2;
		}

		$delay = $delay * MINUTE_IN_SECONDS;

		set_transient( 'ewd-uwpm-send-on-cache', true, $delay );

		$this->send_scheduled_emails();
		$this->send_on_user_x_time_since_login();
		$this->send_on_wc_x_time_since_cart_abandoned();
		$this->send_on_wc_x_time_after_purchase();
	}

	/**
	 * Send one or more emails when someone registers on the site, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_user_registers( $user_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'user_registers' or ! $send_on_action->enabled ) { continue; }

			$args = array(
				'send_type'	=> 'single',
				'user_id' 	=> $user_id, 
				'email_id' 	=> $send_on_action->email_id
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Send one or more emails when someone updates their user profile on the site, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_user_profile_updated( $user_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'user_profile_updated' or ! $send_on_action->enabled ) { continue; }

			$args = array(
				'send_type'	=> 'single',
				'user_id' 	=> $user_id, 
				'email_id' 	=> $send_on_action->email_id
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Send one or more emails when someone has their user role updated on the site, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_user_role_changed( $user_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'user_role_changed' or ! $send_on_action->enabled ) { continue; }

			$args = array(
				'send_type'	=> 'single',
				'user_id' 	=> $user_id, 
				'email_id' 	=> $send_on_action->email_id
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Replace the content of the email that is sent when someone updates their password on the site, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_user_password_reset( $email_content, $user, $userdata ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'user_password_reset' or ! $send_on_action->enabled ) { continue; }

			$args = array(
				'target'			=> 'user',
				'user_id' 			=> $userdata['ID'], 
				'email_id' 			=> $send_on_action->email_id,
				'return_content'	=> true
			);

			$email_content['message'] = $this->email_user( $args );
			$email_content['subject'] = get_the_title( $send_on_action->email_id );
			$Email_Content['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );
		}

		return $email_content;
	}

	/**
	 * Send one or more emails when someone hasn't logged on to the site for a certain amount of time, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_user_x_time_since_login() {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'user_x_time_since_login' or ! $send_on_action->enabled ) { continue; }

			$seconds_since_login = $this->convert_send_on_interval( $send_on_action->interval_unit, $send_on_action->interval_count );
		
			$login_cutoff = time() - $seconds_since_login;

			$users = $ewd_uwpm_controller->database_manager->get_users_without_login( $login_cutoff );
		
			foreach ( $users as $user ) {

				$args = array(
					'target'	=> isset( $send_on_action->target ) ? $send_on_action->target : 'user',
					'user_id' 	=> $user->user_id, 
					'email_id' 	=> $send_on_action->email_id
				);

				$this->email_user( $args );

				update_user_meta( $user->user_id, 'EWD_UWPM_Login_Reminder_Sent', 'Yes' );
			}
		}
	}

	/**
	 * Send one or more emails when a new post is published on the site, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_post_published( $post_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		$user = get_user_by( 'id', get_post_field( 'post_author', $post_id ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'post_published' or ! $send_on_action->enabled ) { continue; }

			$args = array(
				'send_type'	=> 'single',
				'user_id' 	=> $user->user_id, 
				'email_id' 	=> $send_on_action->email_id,
				'post_id'	=> $post_id,
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Saves a post_id to a transient, so that interest emails can be sent out using the categories on next page load
	 *
	 * @note: This is a workaround, since wp_get_post_categories() is returning incorrectly when the post is 
	 * published initially
	 *
	 * @since 1.0.0
	 */
	public function save_post_to_send_on_post_published_interest( $post_id ) {

		set_transient( 'ewd-uwpm-send-post-published-interest-emails', $post_id, 3600 );
	}

	/**
	 * Send one or more emails when a new post is published on the site, if users have indicated an interest and if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_post_published_interest() {
		global $ewd_uwpm_controller;

		$post_id = get_transient( 'ewd-uwpm-send-post-published-interest-emails' );

		if ( empty( $post_id ) ) { return; }

		delete_transient( 'ewd-uwpm-send-post-published-interest-emails' );

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'post_published_interest' or ! $send_on_action->enabled ) { continue; }

			$categories = wp_get_post_categories( $post_id );
	
			$args = array(
				'send_type'		=> 'list',
				'list_id' 		=> -2,
				'email_id' 		=> $send_on_action->email_id, 
				'post_id' 		=> $post_id,
				'interests' 	=> array(
					'post_categories' 	=> $categories,
					'uwpm_categories' 	=> array(),
					'wc_categories' 	=> array()
				),
				'woocommerce' 	=> array(
					'previous_purchasers' 		=> false,
					'product_purchasers' 		=> false,
					'previous_wc_products' 		=> '',
					'category_purchasers' 		=> false, 
					'previous_wc_categories' 	=> ''
				)
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Send one or more emails when a new comment is made on a post, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_new_comment_on_post( $comment_id, $comment_approved = 0 ) {
		global $ewd_uwpm_controller;

		if ( $comment_approved == 'spam' ) { return; }

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'new_comment_on_post' or ! $send_on_action->enabled ) { continue; }

			$current_comment = get_comment( $comment_id );

			$args = array( 
				'post_id' => $current_comment->comment_post_id 
			);

			$all_post_comments = get_comments( $args );
	
			foreach ( $all_post_comments as $comment ) {

				if ( $comment->user_id != 0 and $comment->user_id != $current_comment->user_id ) {

					$args = array(
						'send_type'	=> 'single',
						'user_id' 	=> $comment->user_id, 
						'email_id' 	=> $send_on_action->email_id, 
						'post_id' 	=> $current_comment->comment_post_id 
					);

					$this->handle_send_or_schedule( $send_on_action, $args );
				}
			}
		}
	}

	/**
	 * Send one or more emails when a custom post is published, updated or deleted on the site, if selected
	 *
	 * @since 1.2.0
	 */
	public function cpt_send_on_post_action( $post_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		$user = get_user_by( 'id', get_post_field( 'post_author', $post_id ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( ! in_array( $send_on_action->action_type, array( 'cpt_created', 'cpt_updated', 'cpt_deleted' ) ) or ! $send_on_action->enabled ) { continue; }

			if ( get_post_type( $post_id ) != $send_on_action->cpt_select ) { continue; }

			$args = array(
				'send_type'	=> 'single',
				'user_id' 	=> $user->user_id, 
				'email_id' 	=> $send_on_action->email_id,
				'post_id'	=> $post_id,
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Send one or more emails when a WooCommerce cart is abandoned, potentially depending on products in cart, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_wc_x_time_since_cart_abandoned() {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'wc_x_time_since_cart_abandoned' or ! $send_on_action->enabled ) { continue; }

			$seconds_since_cart_action = $this->convert_send_on_interval( $send_on_action->interval_unit, $send_on_action->interval_count );

			$abandoned_cutoff = time() - $seconds_since_cart_action;

			$users = $ewd_uwpm_controller->database_manager->get_cart_abandoned_users( $abandoned_cutoff );

			foreach ( $users as $user) {

				$includes_match =  $send_on_action->includes == 'any' ? true : false ;

				if ( ! $includes_match ) {

					$product_id = substr( $send_on_action->includes, 0, 1 ) == 'p' ? intval( substr( $send_on_action->includes, 2 ) ) : 0;
					$product_cat = substr( $send_on_action->includes, 0, 1 ) == 'c' ? intval( substr( $send_on_action->includes, 2 ) ) : 0;

					$user_cart = $ewd_uwpm_controller->woocommerce->get_woocommerce_cart( $user );
					
					if ( $user_cart ){

						$user_cart = unserialize( $user_cart );

						if ( is_array( $user_cart ) ){

							foreach ( $user_cart['cart'] as $cart_item ) {

								if ( $product_id ) {
									
									if ( $cart_item['product_id'] == $product_id ) { $includes_match = true; }
									
								} elseif ( $product_cat ){
									
									$product_categories = get_the_terms( $cart_item['product_id'], 'product_cat' );

									foreach ( $product_categories as $category ) {

										if ( $category->term_id == $product_cat ) { $includes_match = true; }
									}
								}

								if ( $includes_match ) { break; }
							}
						}
					}
				}

				if ( ! $includes_match ) { continue; }

				$args = array(
					'target'	=> isset( $send_on_action->target ) ? $send_on_action->target : 'user',
					'user_id' 	=> $user->user_id, 
					'email_id' 	=> $send_on_action->email_id
				);

				$this->email_user( $args );

				update_user_meta( $user->user_id, 'EWD_UWPM_Abandoned_Cart_Reminder_Sent', 'Yes' );
			}
		}
	}

	/**
	 * Send one or more emails when a specified amount of time after a WooCommerce purchase, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_wc_x_time_after_purchase() {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'wc_x_time_after_purchase' or ! $send_on_action->enabled ) { continue; }

			$seconds_since_order = $this->convert_send_on_interval( $send_on_action->interval_unit, $send_on_action->interval_count );

			$order_cutoff = time() - $seconds_since_order;

			$orders = $ewd_uwpm_controller->woocommerce->get_recent_woocommerce_orders( $send_on_action->email_id, $order_cutoff );

			foreach ( $orders as $order ) {

				$user_id = get_post_meta( $order->post_id, '_customer_user', true );

				$emails_sent = (array) get_post_meta( $order->post_id, 'EWD_UWPM_Emails_Sent', true );

				$emails_sent[] = $send_on_action->email_id;
	
				update_post_meta( $order->post_id, 'EWD_UWPM_Emails_Sent', $emails_sent );

				if ( empty( $user_id ) ) { continue; }

				if ( ! $this->check_product_includes( $order->post_id, $send_on_action ) ) { continue; }

				$args = array(
					'target'	=> isset( $send_on_action->target ) ? $send_on_action->target : 'user',
					'user_id' 	=> $user->user_id, 
					'email_id' 	=> $send_on_action->email_id
				);

				$this->email_user( $args );
			}
		}
	}

	/**
	 * Send one or more emails when a new product is published on the site, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_new_product_added( $post_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'product_added' or ! $send_on_action->enabled ) { continue; }

			$wc_categories = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );

			$previous_wc_products = substr( $send_on_action->includes, 0, 1 ) == 'p' ? substr( $send_on_action->includes, 2 ) : '';

			$previous_wc_categories = substr( $send_on_action->includes, 0, 1 ) == 'c' ? substr( $send_on_action->includes, 2 ) : '';

			$args = array(
				'send_type'		=> 'list',
				'list_id' 		=> -2,
				'email_id' 		=> $send_on_action->email_id, 
				'post_id' 		=> $post_id,
				'interests' 	=> array(
					'post_categories' 	=> array(),
					'uwpm_categories' 	=> array(),
					'wc_categories' 	=> $wc_categories
				),
				'woocommerce' 	=> array(
					'previous_purchasers' 		=> $send_on_action->includes == 'any' ? true : false,
					'product_purchasers' 		=> empty( $previous_wc_products ) ? false : true,
					'previous_wc_products' 		=> $previous_wc_products,
					'category_purchasers' 		=> empty( $previous_wc_categories ) ? false : true, 
					'previous_wc_categories' 	=> $previous_wc_categories
				)
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Send one or more emails when a new order is made on the site, if selected
	 *
	 * @since 1.0.0
	 */
	public function send_on_product_purchased( $post_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		if ( ! class_exists( 'WC_Order' ) ) { return; }
	
		$user_id = get_post_meta( $post_id, '_customer_user', true );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'product_purchased' or ! $send_on_action->enabled ) { continue; }

			if ( ! $this->check_product_includes( $post_id, $send_on_action ) ) { continue; }

			$args = array(
				'send_type'		=> 'single',
				'user_id' 		=> $user_id, 
				'email_id' 		=> $send_on_action->email_id
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Send one or more emails when a new subscription is created on the site, if selected
	 *
	 * @since 1.0.17
	 */
	public function send_on_subscription_created( $post_id ) {
		global $ewd_uwpm_controller;

		$send_on_actions = ewd_uwpm_decode_infinite_table_setting( $ewd_uwpm_controller->settings->get_setting( 'send-actions' ) );

		if ( ! class_exists( 'WC_Subscription' ) ) { return; }
	
		$user_id = get_post_meta( $post_id, '_customer_user', true );

		foreach ( $send_on_actions as $send_on_action ) {

			if ( $send_on_action->action_type != 'subscription_created' or ! $send_on_action->enabled ) { continue; }

			if ( ! $this->check_product_includes( $post_id, $send_on_action ) ) { continue; }

			$args = array(
				'send_type'		=> 'single',
				'user_id' 		=> $user_id, 
				'email_id' 		=> $send_on_action->email_id
			);

			$this->handle_send_or_schedule( $send_on_action, $args );
		}
	}

	/**
	 * Sends a notification to the admin when an email error occurs, if enabled
	 *
	 * @since 1.2.0
	 */
	public function maybe_notify_wp_mail_error( $wp_error ) {
		global $ewd_uwpm_controller;

		if ( ! is_wp_error( $wp_error ) ) { return; }

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'notify-admin-on-email-error' ) ) ) { return; }
	  
	    $mail_data = $wp_error->get_error_data();

	    $template_tags = array(
			'{date}'			=> date( 'Y-m-d H:i:s' ),
			'{subject}'			=> $mail_data['subject'],
			'{message}'			=> $mail_data['message'],
			'{status}'			=> $wp_error->get_error_message(),
			'{recipient}'		=> is_array( $mail_data['to'] ) ? implode( ', ', $mail_data['to'] ) : $mail_data['to'],
	    	'{headers}'			=> $mail_data['headers'],
	    	'{attachments}'		=> $mail_data['attachments'],
	    	'{site_link}'		=> '<a href="' . home_url( '/' ) . '">' . get_bloginfo( 'name' ) . '</a>',
		);

		$params = array(
			'log_event' 	=> false,
			'email_address'	=> $ewd_uwpm_controller->settings->get_setting( 'admin-email' ),
			'email_title'	=> str_replace( array_keys( $template_tags ), array_values( $template_tags ), $ewd_uwpm_controller->settings->get_setting( 'notify-admin-subject' ) ),
			'email_content'	=> str_replace( array_keys( $template_tags ), array_values( $template_tags ), $ewd_uwpm_controller->settings->get_setting( 'notify-admin-message' ) ),
		);

		$this->send_email( $params );
	}

	/**
	 * Determines who, who and how to send an email to
	 *
	 * @since 1.2.0
	 */
	public function handle_send_or_schedule( $send_on_action, $args ) {
		global $ewd_uwpm_controller;

		if ( empty( $args['email_id'] ) ) { return false; }
		
		$delay_time = $this->convert_send_on_interval( $send_on_action->interval_unit, $send_on_action->interval_count );
	
		$target = isset( $send_on_action->target ) ? $send_on_action->target : 'user';

		$message_format = get_post_type( $args['email_id'] ) == EWD_UWPM_SMS_POST_TYPE ? 'sms' : 'email';
	
		if ( ! $delay_time and $target == 'admin' ) {
		
			if ( $message_format == 'sms' ) {

				$args['phone_number'] = $ewd_uwpm_controller->settings->get_setting( 'admin-sms-phone-number' );

				$this->send_sms( $args );
			}
			else {
				
				$args['email_address'] = $ewd_uwpm_controller->settings->get_setting( 'admin-email' );
	
				$this->send_email( $args );
			}
		}
		elseif ( ! $delay_time  ) {
			
			if ( $message_format == 'sms' ) {

				if ( $args['send_type'] == 'single' ) { $this->sms_user( $args ); }
				elseif ( $args['send_type'] == 'list' ) { $this->sms_user_list( $args ); }
				elseif ( $args['send_type'] == 'all' ) { $this->sms_all_users( $args ); }
			}
			else {

				if ( $args['send_type'] == 'single' ) { $this->email_user( $args ); }
				elseif ( $args['send_type'] == 'list' ) { $this->email_user_list( $args ); }
				elseif ( $args['send_type'] == 'all' ) { $this->email_all_users( $args ); }
			}
		}
		else {
	
			$args['target'] = $target;
			$args['send_time'] = time() + $delay_time;
	
			$this->schedule_email_send( $args );
		}
	}

	/**
	 * Converts a unit/count pair into the equivalent number of seconds
	 *
	 * @since 1.0.0
	 */
	public function convert_send_on_interval( $interval_unit, $interval_count ) {

		switch ( strtolower( $interval_unit ) ) {
			case 'weeks':
				$unit_adjustor = WEEK_IN_SECONDS;
				break;
			case 'days':
				$unit_adjustor = DAY_IN_SECONDS;
				break;
			case 'hours':
				$unit_adjustor = HOUR_IN_SECONDS;
				break;
			case 'minutes':
				$unit_adjustor = MINUTE_IN_SECONDS;
				break;
			
			default:
				// Send email right now
				$unit_adjustor = false;
				break;
		}

		return $unit_adjustor ? ($unit_adjustor * $interval_count) : $unit_adjustor;
	}

	/**
	 * Saves an email to be sent at a later time
	 *
	 * @since 1.0.0
	 */
	public function schedule_email_send( $params ) {

		$scheduled_emails = (array) get_option( 'EWD_UWPM_Scheduled_Emails' );

		$date = new DateTime();
		$timezone = $date->getTimezone();

		$email = array(
			'send_time' => $params['send_time'],
			'params' 	=> $params
		);

		$scheduled_emails[] = $email;

		update_option( 'EWD_UWPM_Scheduled_Emails', $scheduled_emails );

	 	return __( 'Email has been scheduled to send at ', 'ultimate-wp-mail' ) . $params['send_time'] . '(' . $timezone->getName() . __( ' server timezone', 'ultimate-wp-mail' ) . ')';
	}

	/**
	 * Sends out any emails that have been scheduled
	 *
	 * @since 1.0.0
	 */
	public function send_scheduled_emails() {
		
		$scheduled_emails = (array) get_option( 'EWD_UWPM_Scheduled_Emails' );

		foreach ( $scheduled_emails as $key => $email ) {

			if ( ! is_array( $email ) ) { unset( $scheduled_emails[ $key ] ); continue; }

			if ( strtotime($email['send_time']) > time() ) { continue; }

			$message_format = get_post_type( $email['params']['email_id'] ) == EWD_UWPM_SMS_POST_TYPE ? 'sms' : 'email';

			if ( array_key_exists( 'send_type', $email ) && $email['send_type'] == 'all' ) {

				if ( $sms ) { $this->sms_all_users( $email['params'] ); }
				else { $this->email_all_users( $email['params'] ); }
			}
			elseif ( array_key_exists( 'send_type', $email ) && $email['send_type'] == 'list' ) {

				if ( $sms ) { $this->sms_user_list( $email['params'] ); }
				else { $this->email_user_list( $email['params'] ); }
			}
			else {

				if ( $sms ) { $this->sms_user( $email['params'] ); }
				else {$this->email_user( $email['params'] ); }
			}

			unset( $scheduled_emails[ $key ] );
		}

		update_option( 'EWD_UWPM_Scheduled_Emails', $scheduled_emails );
	}

	/**
	 * Sends an email out to all site users who haven't unsubscribed
	 *
	 * @since 1.0.0
	 */
	public function email_all_users( $params ) {
		global $ewd_uwpm_controller;

		$this->add_email_filters();
		
		$email = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$email_title = empty( $params['email_title'] ) ? $email->post_title : $params['email_title'];
		$email_title = apply_filters( 'ewd_uwpm_title', stripslashes( $email_title ), array() );

		$email_content = empty( $params['email_content'] ) ? get_post_meta( $email->ID, 'EWD_UWPM_Mail_Content', true ) : $params['email_content'];

		$headers = apply_filters( 'ewd_uwpm_headers', array( 'Content-Type: text/html; charset=UTF-8' ), $params );
	
		$params['event_id'] = $ewd_uwpm_controller->database_manager->get_next_send_event_id();
	
		$sent_emails = 0;

		$users = get_users();

		foreach ( $users as $user ) {	
	
			if ( $this->user_is_unsubscribed( $user ) ) { continue; }

			$email_address = $user->user_email;
	
			$params['unique_identifier'] = ewd_random_string( 20 );
	
			$user_email_content = $this->process_email_content( $email_content, $params, $user );

			$email_result = $ewd_uwpm_controller
				->email_queue
				->push_to_queue( array(
					'to'      => $email_address,
					'subject' => $email_title,
					'message' => $user_email_content,
					'headers' => $headers
				) )
				->save()
				->dispatch();


			if ( $email_result ) {
	
				if ( isset( $params['log_event'] ) and $params['log_event'] !== false ) { $ewd_uwpm_controller->database_manager->insert_send_event( $params ); }

				do_action( 'ewd_uwpm_email_sent', $params );
	
				$sent_emails++;
			}
		}
	
		return sprintf( esc_html__( 'Emails successfully sent out to %d users', 'ultimate-wp-mail' ), $sent_emails );
	}

	/**
	 * Sends sms messages out to all site users who haven't unsubscribed
	 *
	 * @since 1.2.0
	 */
	public function sms_all_users( $params ) {
		global $ewd_uwpm_controller;
				
		$sms = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$sms_content = empty( $params['email_content'] ) ? get_post_meta( $sms->ID, 'EWD_UWPM_SMS_Content', true ) : $params['email_content'];
	
		$params['event_id'] = $ewd_uwpm_controller->database_manager->get_next_send_event_id();

		$params['sms'] = true;
	
		$sent_messages = 0;

		$users = get_users();

		foreach ( $users as $user ) {	
				
			if ( $this->user_is_unsubscribed( $user ) ) { continue; }

			$phone_number = $this->get_user_phone_number( $user );

			if ( empty( $phone_number ) ) { continue; }

			$params['unique_identifier'] = ewd_random_string( 20 );

			$this->params = $params;

			$user_sms_content = $this->replace_variables( $sms_content );

			$sms_result = $ewd_uwpm_controller
				->sms_queue
				->push_to_queue( array(
					'to'      => $phone_number,
					'message' => $user_sms_content,
				) )
				->save()
				->dispatch();

			if ( $sms_result ) {
	
				if ( isset( $params['log_event'] ) and $params['log_event'] !== false ) { $ewd_uwpm_controller->database_manager->insert_send_event( $params ); }

				do_action( 'ewd_uwpm_sms_sent', $params );
	
				$sent_messages++;
			}
		}

		return sprintf( esc_html__( 'SMS Messages successfully sent out to %d users', 'ultimate-wp-mail' ), $sent_messages );
	}

	/**
	 * Sends an email to all users in an admin-created list of users, who haven't unsubscribed
	 *
	 * @since 1.0.0
	 */
	public function email_user_list( $params ) {
		global $ewd_uwpm_controller;
	
		$this->add_email_filters();
		
		$email = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$email_title = empty( $params['email_title'] ) ? $email->post_title : $params['email_title'];
		$email_title = apply_filters( 'ewd_uwpm_title', stripslashes( $email_title ), array() );

		$email_content = empty( $params['email_content'] ) ? get_post_meta( $email->ID, 'EWD_UWPM_Mail_Content', true ) : $params['email_content'];

		$headers = apply_filters( 'ewd_uwpm_headers', array( 'Content-Type: text/html; charset=UTF-8' ), $params );
	
		$params['event_id'] = $ewd_uwpm_controller->database_manager->get_next_send_event_id();
	
		$email_lists = (array) get_option( 'ewd-uwpm-email-lists' );

		if ( $params['list_id'] == -2 ) { $list_users = $this->autogenerated_list( $params ); }
		else {

			foreach ( $email_lists as $email_list ) {

				if ( $email_list->id == $params['list_id'] ) { $list_users = $email_list->user_list; }
			}
		}
	
		$sent_emails = 0;

		foreach ( $list_users as $list_user ) {

			$user = is_object( $list_user ) ? get_user_by( 'id', $list_user->id ) : get_user_by( 'id', intval( $list_user ) );	
	
			if ( $this->user_is_unsubscribed( $user ) ) { continue; }

			$email_address = $user->user_email;
	
			$params['unique_identifier'] = ewd_random_string( 20 );
		
			$user_email_content = $this->process_email_content( $email_content, $params, $user );

			$email_result = $ewd_uwpm_controller
				->email_queue
				->push_to_queue( array(
					'to'      => $email_address,
					'subject' => $email_title,
					'message' => $user_email_content,
					'headers' => $headers
				) )
				->save()
				->dispatch();

			if ( $email_result ) {
	
				if ( isset( $params['log_event'] ) and $params['log_event'] !== false ) { $ewd_uwpm_controller->database_manager->insert_send_event( $params ); }

				do_action( 'ewd_uwpm_email_sent', $params );
	
				$sent_emails++;
			}
		}
	
	
		return sprintf( esc_html__( 'Emails successfully sent out to %d users', 'ultimate-wp-mail' ), $sent_emails );
	}


	/**
	 * Sends sms messages out to all users in an admin-created list of users, who haven't unsubscribed
	 *
	 * @since 1.2.0
	 */
	public function sms_user_list( $params ) {
		global $ewd_uwpm_controller;
		
		$sms = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$sms_content = empty( $params['email_content'] ) ? get_post_meta( $sms->ID, 'EWD_UWPM_SMS_Content', true ) : $params['email_content'];
	
		$params['event_id'] = $ewd_uwpm_controller->database_manager->get_next_send_event_id();

		$params['sms'] = true;
	
		$sent_messages = 0;

		$email_lists = (array) get_option( 'ewd-uwpm-email-lists' );

		if ( $params['list_id'] == -2 ) { $list_users = $this->autogenerated_list( $params ); }
		else {

			foreach ( $email_lists as $email_list ) {

				if ( $email_list->id == $params['list_id'] ) { $list_users = $email_list->user_list; }
			}
		}

		foreach ( $list_users as $list_user ) {

			$user = is_object( $list_user ) ? get_user_by( 'id', $list_user->id ) : get_user_by( 'id', intval( $list_user ) );	
	
			if ( $this->user_is_unsubscribed( $user ) ) { continue; }

			$phone_number = $this->get_user_phone_number( $user );

			if ( empty( $phone_number ) ) { continue; }

			$params['unique_identifier'] = ewd_random_string( 20 );

			$this->params = $params;

			$user_sms_content = $this->replace_variables( $sms_content );

			$sms_result = $ewd_uwpm_controller
				->sms_queue
				->push_to_queue( array(
					'to'      => $phone_number,
					'message' => $user_sms_content,
				) )
				->save()
				->dispatch();

			if ( $sms_result ) {
	
				if ( isset( $params['log_event'] ) and $params['log_event'] !== false ) { $ewd_uwpm_controller->database_manager->insert_send_event( $params ); }

				do_action( 'ewd_uwpm_sms_sent', $params );
	
				$sent_messages++;
			}
		}

		return sprintf( esc_html__( 'SMS Messages successfully sent out to %d users', 'ultimate-wp-mail' ), $sent_messages );
	}

	/**
	 * Sends an email to a specific user
	 *
	 * @since 1.0.0
	 */
	public function email_user( $params ) {
		global $ewd_uwpm_controller;

		if ( empty( $params['user_id'] ) ) { return false; }

		$user = get_user_by( 'id', $params['user_id'] );

		if ( $this->user_is_unsubscribed( $user ) and $params['target'] != 'admin' ) { return; }

		$this->add_email_filters();

		$params['unique_identifier'] = ewd_random_string( 20 );
	
		$email = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$email_title = empty( $params['email_title'] ) ? $email->post_title : $params['email_title'];
		$email_title = apply_filters( 'ewd_uwpm_title', stripslashes( $email_title ), array() );

		$email_content = empty( $params['email_content'] ) ? get_post_meta( $email->ID, 'EWD_UWPM_Mail_Content', true ) : $params['email_content'];
		$email_content = $this->process_email_content( $email_content, $params, $user );

		$headers = apply_filters( 'ewd_uwpm_headers', array( 'Content-Type: text/html; charset=UTF-8' ), $params );
	
		$email_address = $params['target'] != 'admin' ? $user->user_email : $ewd_uwpm_controller->settings->get_setting( 'admin-email' );
	
		if ( ! empty( $params['return_email'] ) ) {

			return $email_content;
		}

		$email_result = $ewd_uwpm_controller
			->email_queue
			->push_to_queue( array(
				'to'      => $email_address,
				'subject' => $email_title,
				'message' => $email_content,
				'headers' => $headers
			) )
			->save()
			->dispatch();

		if ( $email_result ) {
			
			$params['event_id'] = $ewd_uwpm_controller->database_manager->get_next_send_event_id();
	
			if ( isset( $params['log_event'] ) and $params['log_event'] !== false ) { $ewd_uwpm_controller->database_manager->insert_send_event( $params ); }

			do_action( 'ewd_uwpm_email_sent', $params );
	
			return __( 'Email successfully sent to ', 'ultimate-wp-mail') . $user->user_login;
		}
		
		return __( 'Email failed to send', 'ultimate-wp-mail' );
	}

	/**
	 * Send an sms message to a user
	 *
	 * @since 1.2.0
	 */
	public function sms_user( $params ) {
		global $ewd_uwpm_controller;

		if ( empty( $params['user_id'] ) ) { return false; }

		$user = get_user_by( 'id', $params['user_id'] );

		if ( $this->user_is_unsubscribed( $user ) and $params['target'] != 'admin' ) { return; }

		$params['unique_identifier'] = ewd_random_string( 20 );

		$params['sms'] = true;

		$this->params = $params;
	
		$sms = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$sms_content = empty( $params['email_content'] ) ? get_post_meta( $sms->ID, 'EWD_UWPM_SMS_Content', true ) : $params['email_content'];
		$sms_content = $this->replace_variables( $sms_content );

		$phone_number = $this->get_user_phone_number( $user );

		if ( empty( $phone_number ) ) { return; }
	
		if ( ! empty( $params['return_email'] ) ) {

			return $email_content;
		}

		$sms_result = $ewd_uwpm_controller
			->sms_queue
			->push_to_queue( array(
				'to'      => $phone_number,
				'message' => $sms_content,
			) )
			->save()
			->dispatch();

		if ( $sms_result ) {
			
			$params['event_id'] = $ewd_uwpm_controller->database_manager->get_next_send_event_id();
	
			if ( isset( $params['log_event'] ) and $params['log_event'] !== false ) { $ewd_uwpm_controller->database_manager->insert_send_event( $params ); }

			do_action( 'ewd_uwpm_sms_sent', $params );
	
			return __( 'SMS successfully sent to ', 'ultimate-wp-mail') . $user->user_login;
		}
		
		return __( 'SMS failed to send', 'ultimate-wp-mail' );
	}

	/**
	 * Send an email generically (e.g. to admin, to a non-user, as a test, etc.)
	 *
	 * @since 1.0.0
	 */
	public function send_email( $params ) {
		global $ewd_uwpm_controller;

		if ( empty( $params['email_address'] ) ) { return false; }

		if ( empty( $params['email_content'] ) and empty( $params['email_title'] ) and empty( $params['email_id'] ) ) { return false; }

		$email = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$email_title = empty( $params['email_title'] ) ? $email->post_title : $params['email_title'];
		$email_title = apply_filters( 'ewd_uwpm_title', stripslashes( $email_title ), array() );

		$email_content = empty( $params['email_content'] ) ? get_post_meta( $email->ID, 'EWD_UWPM_Mail_Content', true ) : $params['email_content'];
		$email_content = $this->process_email_content( $email_content, $params );

		$this->add_email_filters();

		$params['unique_identifier'] = ewd_random_string( 20 );
	
		$headers = apply_filters( 'ewd_uwpm_headers', array( 'Content-Type: text/html; charset=UTF-8' ), $params);

		$email_result = $ewd_uwpm_controller
			->email_queue
			->push_to_queue( array(
				'to'      => $params['email_address'],
				'subject' => $email_title,
				'message' => $email_content,
				'headers' => $headers
			) )
			->save()
			->dispatch();
		
		if ( $email_result ) {

			do_action( 'ewd_uwpm_email_sent', $params );

			return true;
		}
		else {

			return false;
		}
	}

	/**
	 * Send an sms message generically (e.g. to admin, to a non-user, as a test, etc.)
	 *
	 * @since 1.2.0
	 */
	public function send_sms( $params ) {
		global $ewd_uwpm_controller;

		if ( empty( $params['phone_number'] ) ) { return false; }

		if ( empty( $params['email_content'] ) and empty( $params['email_id'] ) ) { return false; }

		$params['unique_identifier'] = ewd_random_string( 20 );

		$params['sms'] = true;

		$this->params = $params;

		$sms = empty( $params['email_id'] ) ? new stdClass() : get_post( $params['email_id'] );

		$sms_content = empty( $params['email_content'] ) ? get_post_meta( $sms->ID, 'EWD_UWPM_SMS_Content', true ) : $params['email_content'];
		$sms_content = $this->replace_variables( $sms_content );
		
		$sms_result = $ewd_uwpm_controller
			->sms_queue
			->push_to_queue( array(
				'to'      => $params['phone_number'],
				'message' => $sms_content,
			) )
			->save()
			->dispatch();
		
		if ( $sms_result ) {

			do_action( 'ewd_uwpm_sms_sent', $params );

			return true;
		}
		else {

			return false;
		}
	}

	/**
	 * Returns a list of all users meeting a certain critera (interest, WooCommerce history, etc.)
	 *
	 * @since 1.0.0
	 */
	public function autogenerated_list( $params ) {
		global $wpdb;
	
		$interests = empty( $params['interests'] ) ? array() : $params['interests'];
		$woocommerce = empty( $params['woocommerce'] ) ? array() : $params['woocommerce'];
		
		$user_ids = array();

		foreach ( $interests as $interest_type => $categories ) {

			if ( $interest_type == 'post_categories' ) {

				$meta_key = 'EWD_UWPM_Post_Interests';
			}
			elseif ( $interest_type == 'UWPM_Categories' ) {

				$meta_key = 'EWD_UWPM_UWPM_Interests';
			}
			else {

				$meta_key = 'EWD_UWPM_WC_Interests';
			}
	
			foreach ( $categories as $category ) {

				if ( empty( $category ) ) { continue; }
	
				$new_users = $wpdb->get_results( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key=%s AND meta_value LIKE %s", $meta_key, '%"' . $category . '"%' ) );

				foreach ( $new_users as $user ) { $user_ids[] = $user->user_id; }
			}
		} 
	
		if ( ! empty( $woocommerce['previous_purchasers'] ) ) {

			$users = $wpdb->get_results( "SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key='_customer_user'" );

			foreach ( $users as $user ) { $user_ids[] = $user->meta_value; }
		}
	
		if ( ! empty( $woocommerce['product_purchasers'] ) ) {

			$woocommerce['previous_wc_products'] = (array) $woocommerce['previous_wc_products'];
			
			$product_id_string = '(' . implode( ',', $woocommerce['previous_wc_products'] ) . ')';
	
			$woocommerce_order_items_table_name = $wpdb->prefix . 'woocommerce_order_items';
			$woocommerce_order_itemmeta_table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
			
			$users = strlen( $product_id_string ) > 2 ? $wpdb->get_results(
					"SELECT DISTINCT $wpdb->postmeta.meta_value FROM $wpdb->postmeta 
					INNER JOIN $woocommerce_order_items_table_name ON $wpdb->postmeta.post_id = $woocommerce_order_items_table_name.order_id
					INNER JOIN $woocommerce_order_itemmeta_table_name ON $woocommerce_order_items_table_name.order_item_id = $woocommerce_order_itemmeta_table_name.order_item_id
					WHERE $woocommerce_order_itemmeta_table_name.meta_key = '_product_id' 
					AND $woocommerce_order_itemmeta_table_name.meta_value IN $product_id_string
					AND $wpdb->postmeta.meta_key = '_customer_user'"
			) : array();

			foreach ( $users as $user ) { $user_ids[] = $user->meta_value; }
		}
	
		if ( ! empty( $woocommerce['category_purchasers'] ) ) {

			$woocommerce['previous_wc_categories'] = (array) $woocommerce['previous_wc_categories'];
			
			$args = array( 
				'posts_per_page' 	=> -1,
				'tax_query' 		=> array(
					array(
						'taxonomy' 			=> 'product_cat',
						'field' 			=> 'term_id',
						'terms' 			=> $woocommerce['previous_wc_categories'],
						'include_children' 	=> false
					)
				)
			);
			
			$product_query = new WP_Query( $args );
			$products = $product_query->get_posts();
	
			$product_ids = array();
			foreach ( $products as $product ) { $product_ids[] = $product->ID; }

			$product_ids = array_unique( $product_ids );
			
			$product_id_string = '(' . implode( ',', $product_ids ) . ')';
			
			$woocommerce_order_items_table_name = $wpdb->prefix . 'woocommerce_order_items';
			$woocommerce_order_itemmeta_table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
			
			$users = strlen( $product_id_string ) > 2 ? $wpdb->get_results(
					"SELECT DISTINCT $wpdb->postmeta.meta_value FROM $wpdb->postmeta 
					INNER JOIN $woocommerce_order_items_table_name ON $wpdb->postmeta.post_id = $woocommerce_order_items_table_name.order_id
					INNER JOIN $woocommerce_order_itemmeta_table_name ON $woocommerce_order_items_table_name.order_item_id = $woocommerce_order_itemmeta_table_name.order_item_id
					WHERE $woocommerce_order_itemmeta_table_name.meta_key = '_product_id' 
					AND $woocommerce_order_itemmeta_table_name.meta_value IN $product_id_string
					AND $wpdb->postmeta.meta_key = '_customer_user'"
			) : array();
			
			foreach ( $users as $user ) { $user_ids[] = $user->meta_value; }
		}
	
		return array_unique( $user_ids );
	}

	public function check_product_includes( $post_id, $send_on_action ) {

		if ( empty( $send_on_action->includes ) or $send_on_action->includes == 'any' ) { return true; }

		$order = new WC_Order( $post_id );
		$products = $order->get_items();
		
		if ( substr( $send_on_action->includes, 0, 1) == 'p' ) {

			$include_product = substr( $send_on_action->includes, 2 );

			foreach ( $products as $product ) {

				if ( $include_product == $product->get_product_id() ) { return true; }
			}
		}
		else {

			$include_category = substr( $send_on_action->includes, 2 );

			foreach ( $products as $product ) {

				$cat_ids = wp_get_post_terms( $product->get_product_id(), 'product_cat', array( 'fields' => 'ids' ) );

				if ( in_array( $include_category, $cat_ids ) ) { return true; }
			}
		}

		return false;
	}

	/**
	 * Returns a phone number, if it exists for a user
	 *
	 * @since 1.2.0
	 */
	public function get_user_phone_number( $user ) {
		global $ewd_uwpm_controller;

		if ( ! empty( get_user_meta( $user->ID, 'ewd_uwpm_phone_number', true ) ) ) { return get_user_meta( $user->ID, 'ewd_uwpm_phone_number', true ); }

		if ( ! empty( get_user_meta( $user->ID, 'billing_phone', true ) ) ) { return get_user_meta( $user->ID, 'billing_phone', true ); }

		if ( ! empty( get_user_meta( $user->ID, 'shipping_phone', true ) ) ) { return get_user_meta( $user->ID, 'shipping_phone', true ); }

		return null;
	}

	/**
	 * Get the email content ready for sending, allow filtering
	 *
	 * @since 1.0.0
	 */
	public function process_email_content( $email_content, $params, $user = null ) {

		if ( empty( $user ) ) { $user = get_user_by( 'email', $params['email_address'] ); }

		$params['user_id'] = ! empty( $params['user_id'] ) ? $params['user_id'] : ( ! empty( $user ) ? $user->ID : 0 );

		$this->params = $params;

		$email_content = apply_filters( 'ewd_uwpm_content_pre_substitutions', $email_content, $params );
		$email_content = $this->replace_classes( $email_content );

		$email_content = $this->replace_variables( $email_content );
		$email_content = apply_filters( 'ewd_uwpm_content_post_substitutions', $email_content, $params );

		return $email_content;
	}

	/**
	 * Replace plugin-defined and custom element tags with content
	 *
	 * @since 1.0.0
	 */
	function replace_variables( $email_content ) {
		global $ewd_uwpm_controller;

		$params = $this->params;

		$user = empty( $params['user_id'] ) ? false : get_user_by( 'id', intval( $params['user_id'] ) );
		$post = empty( $params['post_id'] ) ? false : get_post( intval( $params['post_id'] ) );
	
		$contact_methods = wp_get_user_contact_methods();
	
		$search_array = array(
			'[username]',
			'[fname]',
			'[lname]',
			'[nickname]',
			'[dname]',
			'[email]',
			'[website]',
			'[post_title]',
			'[post_content]',
			'[post_date]',
			'[post_status]',
			'[post_type]'
		);
	
		$replace_array = array(
			$user ? $user->user_login : 'Username',
			$user ? $user->user_firstname : 'Test',
			$user ? $user->user_lastname : 'User',
			$user ? $user->get( 'nickname' ) : 'Testy',
			$user ? $user->display_name : 'Test User',
			$user ? $user->user_email : 'email@example.com',
			$user ? $user->user_url : 'http://example.com',
			$post ? $post->post_title : '',
			$post ? $post->post_content : '',
			$post ? $post->post_date : '',
			$post ? $post->post_status : '',
			$post ? $post->post_type : '',
		);
	
		foreach ( $contact_methods as $key => $contact_method ) {

			$search_array[] = "[" . $key . ']';
			$replace_array[] = $user ? $user->get( $key ) : $contact_method;
		}

		$custom_elements = $ewd_uwpm_controller->custom_element_manager->get_custom_elements();
	
		foreach ( $custom_elements as $custom_element ) {

			if ( empty( $custom_element->callback_function ) ) { continue; }

			$callback_function  = $custom_element->callback_function;
			
			if ( ! empty( $custom_element->attributes ) and is_array( $custom_element->attributes ) ) {

				$pattern = "/\[" . $custom_element->slug . " (.*?)\]/";
				$message = $email_content;
	
				preg_match_all( $pattern, $email_content, $matches );
				
				$attributes = array();

				if ( ! empty( $matches[1] ) ) {
						
					foreach ( $matches[1] as $match ) {

						foreach ( $custom_element->attributes as $attribute ) {

							$attribute_pattern = "/.*" . $attribute['attribute_name'] . "='(.*?)'.*/";

							preg_match( $attribute_pattern, $match, $attribute_match );

							if ( isset( $attribute_match[1] ) ) {

								$attributes[ $attribute['attribute_name'] ] = $attribute_match[1];
							}
		
							if ( ! empty( $attribute['attribute_exact_match'] ) and strtolower( $attribute['attribute_exact_match'] ) == 'yes' ) {

								$pattern = "/\[" . $custom_element->slug . " " . $attribute['attribute_name'] ."='" . $attribute_match[1] . "' \]/";
							}
						}
		
						$params['attributes'] = $attributes;
						$params['slug']	= $custom_element->slug;

						$replace = $callback_function( $params, $user );
		
						$result = preg_replace( $pattern, $replace, $email_content );
		
						unset( $params['attributes'] );

						$email_content = $result;
					}
				}
			}
			else {

				$params['slug']	= $custom_element->slug;
				
				$search_array[] = "[" . $custom_element->slug . "]";
				$replace_array[] = $callback_function( $params, $user );
			}
		}
	
		$modified_email_content = str_replace( $search_array, $replace_array, $email_content );
	
		$processed_email_content = $ewd_uwpm_controller->settings->get_setting( 'track-clicks' ) ? preg_replace_callback( '/href="(.*?)"/', array( $this, 'replace_links_for_tracking' ), $modified_email_content ) : $modified_email_content;
	
		if ( $ewd_uwpm_controller->settings->get_setting( 'add-unsubscribe-link' ) ) {

			$args = array(
				'action'	=> 'ewd_uwpm_unsubscribe',
				'code'		=> ( $user ? $user->ID : 0 ) . 'pl' . round( rand() * 1000 ),
				'email'		=> ( $user ? $user->user_email : 'email@example.com' )
			);

			$unsubscribe_link = add_query_arg( $args, site_url() );

			if ( ! empty( $params['sms'] ) ) {

				$processed_email_content .= ' ' . $unsubscribe_link;
			}
			else {
			
				ob_start();

				?>

				<div id="ewd-uwpm-unsubscribe" style="width: 100% !important; background: #ddd; color: #6e6e6e; text-align: center; padding-top: 8px; height: 26px; margin-top: 20px;">
				
					<a href="<?php echo esc_html( $unsubscribe_link ); ?>" style="color: #6e6e6e; text-decoration: none;">
						<?php echo esc_html( $ewd_uwpm_controller->settings->get_setting( 'label-unsubscribe' ) ); ?>
					</a>

				</div>

				<?php

				$unsubscribe_html = ob_get_clean();

				$processed_email_content .= $unsubscribe_html;
			}
		}
	
		if ( $ewd_uwpm_controller->settings->get_setting( 'track-opens' ) ) {

			$processed_email_content .= '<img src="' . add_query_arg( 'ewd_uwpm_id', $params['unique_identifier'], site_url() ) . '" />';
		}
	
		return $processed_email_content;
	}

	/**
	 * Replace the classes used in the admin with the classes and styling that go in the actual emails
	 *
	 * @since 1.0.0
	 */
	public function replace_classes( $email_content ) {

		$params = $this->params;

		$email_id = empty( $params['email_id'] ) ? 0 : $params['email_id'];
	
		$email_background_color = get_post_meta( $email_id, 'EWD_UWPM_Email_Background_Color', true );
		$email_styling_css = $email_background_color ? 'background-color: ' . $email_background_color . ';' : '';
	
		$max_width = get_post_meta( $email_id, 'EWD_UWPM_Max_Width', true );
		$body_styling_css = $max_width ? 'max-width: ' . $max_width . ';' : 'max-width: 840px;';
		
		$content_alignment = get_post_meta( $email_id, 'EWD_UWPM_Content_Alignment', true );
		$body_styling_css .= $content_alignment == 'center' ? 'margin: 0 auto;' : ( $content_alignment == 'right' ? 'float:right;' : '' );
		
		$body_background_color = get_post_meta( $email_id, 'EWD_UWPM_Body_Background_Color', true );
		$body_styling_css .= $body_background_color ? 'background-color:' . $body_background_color . ';' : '';
	
		$block_background_color = get_post_meta( $email_id, 'EWD_UWPM_Block_Background_Color', true );
		$block_styling_css = $block_background_color ? 'background-color: ' . $block_background_color . ';' : '';

		$block_border = get_post_meta( $email_id, 'EWD_UWPM_Block_Border', true );
		$block_styling_css .= $block_border ? 'border: ' . $block_border . ';' : '';
	
		$search_array = array();
		$replace_array = array();
	
		$search_array[] = 'class="ewd-uwpm-clear';
		$replace_array[] = 'style="clear:both" class="ewd-uwpm-clear';
	
		$search_array[] = 'class="ewd-uwpm-section-container';
		$replace_array[] = 'style="width:90%; max-width:840px; margin:12px 5%;" class="ewd-uwpm-section-container';
	
		$search_array[] = 'class="ewd-uwpm-section width-1';
		$replace_array[] = 'style="width: calc(100% - 24px); ' . $block_styling_css . '" class="ewd-uwpm-section width-1';
		
		$search_array[] = 'class="ewd-uwpm-section width-2';
		$replace_array[] = 'style="width: calc(50% - 30px); float:left; margin-right:30px; ' . $block_styling_css . '" class="ewd-uwpm-section width-2';
	
		$search_array[] = 'class="ewd-uwpm-section width-3';
		$replace_array[] = 'style="width: calc(100% / 3 - 25px); float:left; margin-right:20px; ' . $block_styling_css . '" class="ewd-uwpm-section width-3';
	
		$search_array[] = 'class="ewd-uwpm-section width-4';
		$replace_array[] = 'style="width: calc(25% - 21px); float:left; margin-right:16px; ' . $block_styling_css . '" class="ewd-uwpm-section width-4';
	
		$search_array[] = 'class="ewd-uwpm-section width-1-3';
		$replace_array[] = 'style="width: calc(100% / 3 - 24px); float:left; margin-right:20px; ' . $block_styling_css . '" class="ewd-uwpm-section width-1-3';
	
		$search_array[] = 'class="ewd-uwpm-section width-2-3';
		$replace_array[] = 'style="width: calc(200% / 3 - 24px); float:left; margin-right:20px; ' . $block_styling_css . '" class="ewd-uwpm-section width-2-3';
	
		$modified_message = str_replace( $search_array, $replace_array, stripslashes( $email_content ) );
	
		$final_message = '<div style="' . $email_styling_css . '">';
		$final_message .= '<div style="width:100%; ' . $body_styling_css . '">';
		$final_message .= $modified_message;
		$final_message .= '</div>';
		$final_message .= '<div style="clear:both;"></div>';
		$final_message .= '</div>';
	
		return $final_message;
	}

	/**
	 * Replace the links in an email with links that will point to this site and 
	 * then redirect to the actual destination, to track links clicked
	 * @since 1.0.0
	 */
	public function replace_links_for_tracking( $match ) {
		
		$params = $this->params;
	
	    return 'href="' . add_query_arg( 'ewd_uwpm_id', $params['unique_identifier'], site_url() ) . '&ewd_upwm_link_url=' . urlencode( $match[1] ) . '"';
	}

	/**
	 * Returns true if a user has unsubscribed and if unsubscribe hasn't been disabled
	 * @since 1.0.0
	 */
	public function user_is_unsubscribed( $user ) {
		global $ewd_uwpm_controller;

		if ( get_user_meta( $user->ID, 'EWD_UWPM_User_Unsubscribe', true ) != 'Yes' ) { return false; }

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'add-unsubscribe-link' ) ) and empty( $ewd_uwpm_controller->settings->get_setting( 'add-unsubscribe-checkbox' ) ) ) { return false; }

		return true;
	}

	/**
	 * Add filters for from name and/or email depending on settings
	 * @since 1.0.0
	 */
	public function add_email_filters() {
		global $ewd_uwpm_controller;

		if ( $ewd_uwpm_controller->settings->get_setting( 'email-from-name' ) ) { add_filter( 'wp_mail_from_name', array( $this, 'filter_from_name' ) ); }
		if ( $ewd_uwpm_controller->settings->get_setting( 'email-from-email' ) ) { add_filter( 'wp_mail_from', array( $this, 'filter_from_email' ) ); }
	}

	/**
	 * Filters the from name when enqueued
	 * @since 1.0.0
	 */
	public function filter_from_name( $from_name ) {
		global $ewd_uwpm_controller;

		return $ewd_uwpm_controller->settings->get_setting( 'email-from-name' );
	}

	/**
	 * Filters the from email when enqueued
	 * @since 1.0.0
	 */
	public function filter_from_email( $from_name ) {
		global $ewd_uwpm_controller;

		return $ewd_uwpm_controller->settings->get_setting( 'email-from-email' );
	}

	/**
	 * Change PHPMailer options with ours.
	 *
	 * @param \PHPMailer $phpmailer passed by reference.
	 */
	public function phpmailer_init( $phpmailer ) {
		global $ewd_uwpm_controller;

		if ( ! $ewd_uwpm_controller->settings->get_setting( 'smtp-enable' ) ) {
			return;
		}

		if ( ! $ewd_uwpm_controller->settings->get_setting( 'smtp-host' ) ) {
			return;
		}

		$phpmailer->Mailer = 'smtp';

		if ( 'none' == $ewd_uwpm_controller->settings->get_setting( 'smtp-encryption' ) ) {
			$phpmailer->SMTPSecure = '';
		}
		else {
			$phpmailer->SMTPSecure = $ewd_uwpm_controller->settings->get_setting( 'smtp-encryption' );
		}

		if ( 'tls' != $ewd_uwpm_controller->settings->get_setting( 'smtp-encryption' ) ) {
			$phpmailer->SMTPAutoTLS = true;

			if( $ewd_uwpm_controller->settings->get_setting( 'smtp-autotls-disable' ) ) {
				$phpmailer->SMTPAutoTLS = false;
			}
		}

		$phpmailer->Host = $ewd_uwpm_controller->settings->get_setting( 'smtp-host' );
		$phpmailer->Port = $ewd_uwpm_controller->settings->get_setting( 'smtp-port' );

		// If we're using smtp auth, set the username & password.
		if ( $ewd_uwpm_controller->settings->get_setting( 'smtp-auth' ) ) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $ewd_uwpm_controller->settings->get_setting( 'smtp-username' );
			$phpmailer->Password = $ewd_uwpm_controller->settings->get_setting( 'smtp-password' );
		}
	}

	/**
	 * Add From headers when missing
	 * 
	 * @param array $headers existing list of formatted headers
	 * 
	 * @return array $headers
	 * */
	public function amend_headers( $headers ) {
		global $ewd_uwpm_controller;

		// assume that we need to add the From header
		$add_froms = true;

		// Check if the From header exists
		array_walk( $headers, function( $v, $i ) {
			if( false !== strpos( strtolower( $v ), 'from' ) ) {
				$add_froms = false;
			}
		} );

		// Do not add From header if already exists
		if( !$add_froms ) {
			return $headers;
		}

		// Default From header values
		$name = get_option( 'blogname' );
		$email = $ewd_uwpm_controller->settings->get_setting( 'smtp-username' );

		if( !empty( $ewd_uwpm_controller->settings->get_setting( 'email-from-name' ) ) ) {
			$name = $ewd_uwpm_controller->settings->get_setting( 'email-from-name' );
		}
		
		if( !empty( $ewd_uwpm_controller->settings->get_setting( 'email-from-email' ) ) ) {
			$email = $ewd_uwpm_controller->settings->get_setting( 'email-from-email' );
		}

		// Add From header
		$headers[] = "From: {$name} <$email>";

		return $headers;
	}

	/**
	 * Force the email from field, if SMTP enabled and this is desired
	 * @since 1.2.0
	 */
	public function filter_mail_from_email( $wp_email ) {
		global $ewd_uwpm_controller;

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'smtp-enable' ) ) or empty( $ewd_uwpm_controller->settings->get_setting( 'smtp-auth' ) ) ) { return $wp_email; }

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'smtp-force-from-email' ) ) ) { return $wp_email; }

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'smtp-username' ) ) ) { return $wp_email; }

		return $ewd_uwpm_controller->settings->get_setting( 'smtp-username' );
	}
}
} // endif;

