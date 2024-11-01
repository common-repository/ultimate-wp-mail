<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduwpmWooCommerce' ) ) {
/**
 * Class to handle interactions with the WooCommerce platform
 *
 * @since 1.0.0
 */
class ewduwpmWooCommerce {

	public function __construct() {
		
		add_action( 'uwpm_register_custom_element', 		array( $this, 'maybe_add_woocommerce_elements' ) );
		add_action( 'uwpm_register_custom_element_section', array( $this, 'maybe_add_woocommerce_section' ) );

		add_action( 'woocommerce_add_to_cart', 				array( $this, 'track_current_cart' ) );
		add_action( 'woocommerce_cart_item_removed', 		array( $this, 'track_current_cart' ) );
		add_action( 'woocommerce_cart_item_restored', 		array( $this, 'track_current_cart' ) );
	}

	/**
	 * Adds in the default WooCommerce elements for UWPM, if enabled
	 * @since 1.0.0
	 */
	public function maybe_add_woocommerce_elements() {
		global $ewd_uwpm_controller;

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'woocommerce-integration' ) ) ) { return; }

		$args = array(
			'label' 			=> 'Order Name', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_name'
		);

		uwpm_register_custom_element( 'order_name', $args );

		$args = array(
			'label' 			=> 'Order Status', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_status'
		);

		uwpm_register_custom_element( 'order_status', $args );

		$args = array(
			'label' 			=> 'Order Date', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_date'
		);

		uwpm_register_custom_element( 'order_date', $args );

		$args = array(
			'label' 			=> 'Order Phone Number', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_phone_number'
		);

		uwpm_register_custom_element( 'order_phone', $args );

		$args = array(
			'label' 			=> 'Order Email', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_email'
		);

		uwpm_register_custom_element( 'order_email', $args );

		$args = array(
			'label' 			=> 'Order Billing Details', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_billing_details'
		);

		uwpm_register_custom_element( 'order_billing_details', $args );

		$args = array(
			'label' 			=> 'Order Shipping Details', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_shipping_details'
		);

		uwpm_register_custom_element( 'order_shipping_details', $args );

		$args = array(
			'label' 			=> 'List of Products in Order', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_products_list'
		);

		uwpm_register_custom_element( 'order_products_list', $args );

		$args = array(
			'label' 			=> 'Thumbnails of Products in Order', 
			'section' 			=> 'woocommerce', 
			'callback_function' => 'ewd_uwpm_get_wc_order_products_thumbnails'
		);

		uwpm_register_custom_element( 'order_products_thumbnails', $args );
	}

	/**
	 * Adds in WooCommerce section for UWPM, if enabled
	 * @since 1.0.0
	 */
	public function maybe_add_woocommerce_section() {
		global $ewd_uwpm_controller;

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'woocommerce-integration' ) ) ) { return; }

		$args = array(
			'label' => 'WooCommerce'
		);

		uwpm_register_custom_element_section( 'woocommerce', $args );
	}

	/**
	 * Returns the ID of the last WooCommerce order for a particular user
	 * @since 1.0.0
	 */
	public function get_last_woocommerce_order_id( $user_id ) {
		global $wpdb;

		if ( empty( $user_id ) ) { return false; }

		return $wpdb->get_var(
			"SELECT ID FROM $wpdb->posts
			INNER JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id
			WHERE $wpdb->postmeta.meta_value = $user_id
			AND $wpdb->postmeta.meta_key = '_customer_user'
			ORDER BY $wpdb->posts.post_date DESC
			LIMIT 0,1"
		);
	}

	public function track_current_cart() {
		global $woocommerce;
		global $ewd_uwpm_controller;

		if ( empty( $ewd_uwpm_controller->settings->get_setting( 'woocommerce-integration' ) ) ) { return; }
	
		$user_id = get_current_user_id();

		if ( empty( $user_id ) ) { return; }
	
		$product_ids = array();

		foreach ( $woocommerce->cart->get_cart() as $cart_item ) {

			$product = $cart_item['data'];

	        if ( ! empty( $product ) ) {
	        	
	        	$product_ids[] = $product->get_id();
	        }
		}
	
		if ( ! empty( $product_ids ) ) {

			update_user_meta( $user_id, 'EWD_UWPM_User_Cart_Contents', $product_ids );
			update_user_meta( $user_id, 'EWD_UWPM_User_Cart_Update_Time', time() );
			update_user_meta( $user_id, 'EWD_UWPM_Abandoned_Cart_Reminder_Sent', 'No' );
		}
		else {

			delete_usermeta( $user_id, 'EWD_UWPM_User_Cart_Contents' );
			delete_usermeta( $user_id, 'EWD_UWPM_User_Cart_Update_Time' );
			delete_usermeta( $user_id, 'EWD_UWPM_Abandoned_Cart_Reminder_Sent' );
		}
	}

	public function get_woocommerce_cart( $user ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare(
			"SELECT meta_value FROM $wpdb->usermeta 
			WHERE $wpdb->usermeta.meta_key LIKE '_woocommerce_persistent_cart%'
			AND user_id = %d", 
			$user->user_id 
		) );
	}

	public function get_recent_woocommerce_orders( $email_id, $order_cutoff ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare(
			"SELECT DISTINCT post_id FROM $wpdb->postmeta 
			INNER JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID
			WHERE $wpdb->postmeta.meta_key = 'EWD_UWPM_Emails_Sent'
			AND $wpdb->postmeta.meta_value NOT LIKE %s 
			AND $wpdb->posts.post_date < %s
			AND $wpdb->posts.post_date > %s", 
			'%' . $email_id . '%',
			date( 'Y-m-d H:i:s', $order_cutoff ),
			date( 'Y-m-d H:i:s', $order_cutoff - 24*3600 )
		) );
	}
}
}

/**
 * Returns the name of a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_name( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	$order = get_post( $post_id );

	return $order->post_title;
}

/**
 * Returns the status of a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_status( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	$order = get_post( $post_id );

	return $order->post_status;
}

/**
 * Returns the created date of a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_date( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	$order = get_post( $post_id );

	return $order->post_date;
}

/**
 * Returns the billing phone number of a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_phone_number( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	return get_post_meta( $post_id, '_billing_phone', true );
}

/**
 * Returns the billing email of a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_email( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	return get_post_meta( $post_id, '_billing_email', true );
}

/**
 * Returns the billing details of a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_billing_details( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	$first_name		= get_post_meta( $post_id, '_billing_first_name', true );
	$last_name 		= get_post_meta( $post_id, '_billing_last_name', true );
	$company 		= get_post_meta( $post_id, '_billing_company', true );
	$address_1 		= get_post_meta( $post_id, '_billing_address_1', true );
	$address_2 		= get_post_meta( $post_id, '_billing_address_2', true );
	$city 			= get_post_meta( $post_id, '_billing_city', true );
	$postal_code 	= get_post_meta( $post_id, '_billing_postcode', true );
	$country 		= get_post_meta( $post_id, '_billing_country', true );
	$state 			= get_post_meta( $post_id, '_billing_state', true );
	
	$billing_address = $first_name . " " . $last_name . '<br />';
	$billing_address .= ($company != '' ? $company . '<br />' : '');
	$billing_address .= ($address_1 != '' ? $address_1 . '<br />' : '');
	$billing_address .= ($address_2 != '' ? $address_2 . '<br />' : '');
	$billing_address .= ($city != '' ? $city . ', ' : '') . ($state != '' ? $state . ', ' : '') . ($country != '' ? $country : '') . '<br />';
	$billing_address .= $postal_code;

	return $billing_address;
}

/**
 * Returns the shipping details of a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_shipping_details( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	$first_name		= get_post_meta( $post_id, '_shipping_first_name', true );
	$last_name 		= get_post_meta( $post_id, '_shipping_last_name', true );
	$company 		= get_post_meta( $post_id, '_shipping_company', true );
	$address_1 		= get_post_meta( $post_id, '_shipping_address_1', true );
	$address_2 		= get_post_meta( $post_id, '_shipping_address_2', true );
	$city 			= get_post_meta( $post_id, '_shipping_city', true );
	$postal_code 	= get_post_meta( $post_id, '_shipping_postcode', true );
	$country 		= get_post_meta( $post_id, '_shipping_country', true );
	$state 			= get_post_meta( $post_id, '_shipping_state', true );
	
	$shipping_address = $first_name . " " . $last_name . '<br />';
	$shipping_address .= ($company != '' ? $company . '<br />' : '');
	$shipping_address .= ($address_1 != '' ? $address_1 . '<br />' : '');
	$shipping_address .= ($address_2 != '' ? $address_2 . '<br />' : '');
	$shipping_address .= ($city != '' ? $city . ', ' : '') . ($state != '' ? $state . ', ' : '') . ($country != '' ? $country : '') . '<br />';
	$shipping_address .= $postal_code;

	return $shipping_address;
}

/**
 * Returns all of the products which were in a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_products_list( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	if ( get_post_type( $post_id ) != 'shop_order' ) { return; }

	$order = new WC_Order( $post_id ); 

	$products_list = '';

	$products = $order->get_items();

	foreach ( $products as $product ) {

		$products_list .= $product->get_name() . ',';
	}

	return trim( $products_list, ',' );	
}

/**
 * Returns thumbnail images for all of the products which were in a WooCommerce order
 * @since 1.0.0
 */
function ewd_uwpm_get_wc_order_products_thumbnails( $params, $user = null ) {
	global $ewd_uwpm_controller;

	if ( empty( $user ) ) { return; }

	$post_id = ! empty( $params['post_id'] ) ? $params['post_id'] : $ewd_uwpm_controller->woocommerce->get_last_woocommerce_order_id( $user->ID );

	if ( get_post_type( $post_id ) != 'shop_order' ) { return; }

	$order = new WC_Order( $post_id ); 

	$products_thumbnails = '';

	$products = $order->get_items();

	foreach ( $products as $product ) {

		$products_thumbnails .= $product->get_name() . ',';

		$wc_product = wc_get_product($product->get_product_id());

		if ( empty( $wc_product ) ) { continue; }

		$products_thumbnails .= '<div style="max-width:180px; margin-right:12px; float:left;">';
		$products_thumbnails .= str_replace( '//', 'http://', $wc_product->get_image() );
		$products_thumbnails .= '<a href="' . $wc_product->get_permalink() . '">';
		$products_thumbnails .= $wc_product->get_name();
		$products_thumbnails .= '</a><br/>';
		$products_thumbnails .= $wc_product->get_price_html();
		$products_thumbnails .= '</div>';
	}

	return $products_thumbnails;	
}