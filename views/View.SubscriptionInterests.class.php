<?php

/**
 * Class to display the subscription interest form on the front end.
 *
 * @since 1.0.0
 */
class ewduwpmViewSubscriptionInterest extends ewduwpmView {


	/**
	 * Render the view and enqueue required stylesheets
	 * @since 1.0.0
	 */
	public function render() {
		global $ewd_uwpm_controller;

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		$this->set_interest_options();

		// Add css classes to the slider
		$this->classes = $this->get_classes();

		ob_start();

		$this->add_custom_styling();

		$template = $this->find_template( 'subscription-interests' );
		
		if ( $template ) {
			include( $template );
		}

		$output = ob_get_clean();

		return apply_filters( 'ewd_uwpm_subscription_interests_output', $output, $this );
	}

	/**
	 * Print the login URL, if the user is not logged in
	 *
	 * @since 1.0.0
	 */
	public function maybe_print_login() {

		if ( is_user_logged_in() ) { return; }

		if ( ! $this->display_login_message ) { return; }
		
		$template = $this->find_template( 'subscription-interest-login' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the the possible interests, if the user is logged in
	 *
	 * @since 1.0.0
	 */
	public function maybe_print_interest_topics() {

		if ( ! is_user_logged_in() ) { return; }
		
		$template = $this->find_template( 'subscription-interest-selections' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the the post categories, if selected
	 *
	 * @since 1.0.0
	 */
	public function maybe_print_post_categories() {

		if ( ! is_array( $this->display_interests ) or ! in_array( 'post_categories', $this->display_interests ) ) { return; }
		
		$template = $this->find_template( 'subscription-interest-post-categories' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the the UWPM categories, if selected
	 *
	 * @since 1.0.0
	 */
	public function maybe_print_uwpm_categories() {

		if ( ! is_array( $this->display_interests ) or ! in_array( 'uwpm_categories', $this->display_interests ) ) { return; }
		
		$template = $this->find_template( 'subscription-interest-uwpm-categories' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the the WooCommerce categories, if selected
	 *
	 * @since 1.0.0
	 */
	public function maybe_print_woocommerce_categories() {

		if ( ! is_array( $this->display_interests ) or ! in_array( 'woocommerce_categories', $this->display_interests ) ) { return; }
		
		$template = $this->find_template( 'subscription-interest-woocommerce-categories' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Returns the existing post categories
	 *
	 * @since 1.0.0
	 */
	public function get_post_categories() {

		$args = array(
			'taxonomy' 		=> 'category', 
			'hide_empty' 	=> false,
			'include'		=> ! empty( $this->post_categories ) ? $this->post_categories : array()
		);
		
		$terms = get_terms( $args );

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Returns the existing UWPM categories
	 *
	 * @since 1.0.0
	 */
	public function get_uwpm_categories() {

		$args = array(
			'taxonomy' 		=> EWD_UWPM_EMAIL_CATEGORY_TAXONOMY, 
			'hide_empty' 	=> false,
			'include'		=> ! empty( $this->uwpm_categories ) ? $this->uwpm_categories : array()
		);
		
		$terms = get_terms( $args );

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Returns the existing WooCommerce categories
	 *
	 * @since 1.0.0
	 */
	public function get_woocommerce_categories() {

		$args = array(
			'taxonomy' 		=> 'product_cat', 
			'hide_empty' 	=> false,
			'include'		=> ! empty( $this->woocommerce_categories ) ? $this->woocommerce_categories : array()
		);
		
		$terms = get_terms( $args );

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Returns any post categories the current user is subscribed to
	 *
	 * @since 1.0.0
	 */
	public function get_post_category_interests() {

		return (array) get_user_meta( get_current_user_id(), 'EWD_UWPM_Post_Interests', true );
	}

	/**
	 * Returns any UWPM categories the current user is subscribed to
	 *
	 * @since 1.0.0
	 */
	public function get_uwpm_category_interests() {

		return (array) get_user_meta( get_current_user_id(), 'EWD_UWPM_UWPM_Interests', true );
	}

	/**
	 * Returns any WooCommerce categories the current user is subscribed to
	 *
	 * @since 1.0.0
	 */
	public function get_woocommerce_category_interests() {

		return (array) get_user_meta( get_current_user_id(), 'EWD_UWPM_WC_Interests', true );
	}

	/**
	 * Get the initial subscription interest form css classes
	 * @since 1.0.0
	 */
	public function get_classes( $classes = array() ) {
		global $ewd_uwpm_controller;

		$columns = is_array( $this->display_interests ) ? sizeof( $this->display_interests ) : 1;

		$classes = array_merge(
			$classes,
			array(
				'ewd-uwpm-subscription-interests',
				'ewd-uwpm-subscription-interest-columns-' . $columns
			)
		);

		return apply_filters( 'ewd_uwpm_customer_form_classes', $classes, $this );
	}

	/**
	 * Set a number of variables used in this view
	 * @since 1.0.0
	 */
	public function set_interest_options() {
		global $ewd_uwpm_controller;

		$this->display_interests = $this->display_interests ? array_map( 'trim', explode( ',', $this->display_interests ) ) : $ewd_uwpm_controller->settings->get_setting( 'display-interests' );
		$this->display_login_message = strtolower( $this->display_login_message ) == 'no' ? false : true;
	}

	/**
	 * Enqueue the necessary CSS and JS files
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		global $ewd_uwpm_controller;

		wp_enqueue_style( 'ewd-uwpm-css' );

		wp_enqueue_script( 'ewd-uwpm-js' );
	}
}
