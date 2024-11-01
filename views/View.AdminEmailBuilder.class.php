<?php

/**
 * Class to display an email editor in the admin.
 *
 * @since 1.0.0
 */
class ewduwpmAdminEmailBuilderView extends ewduwpmView {

	// The WP Post for the email being edited
	public $post;

	// The nonce used for the email editor
	public $nonce;

	// Email content
	public $content;
	public $plain_text_content;

	// Email styling
	public $content_alignment;
	public $max_width;
	public $background_color;
	public $body_background_color;
	public $block_background_color;
	public $block_border;


	/**
	 * Render the view and enqueue required stylesheets
	 * @since 1.0.0
	 */
	public function render() {
		global $ewd_uwpm_controller;

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		$this->get_email_options();

		ob_start();

		$template = $this->find_template( 'admin-email-builder' );
		
		if ( $template ) {
			include( $template );
		}

		$output = ob_get_clean();

		return apply_filters( 'ewd_uwpm_admin_email_builder_output', $output, $this );
	}

	/**
	 * Print the area where results of sending an email are displayed
	 *
	 * @since 1.0.0
	 */
	public function print_send_results() {
		
		$template = $this->find_template( 'admin-send-results' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the area where columns can be selected and full-screen mode can be toggled
	 *
	 * @since 1.0.0
	 */
	public function print_header_bar() {
		
		$template = $this->find_template( 'admin-header-bar' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Prints the template options, if no email content exists
	 *
	 * @since 1.0.0
	 */
	public function maybe_print_template_selector() {

		if ( ! empty( $this->content ) ) { return; }
		
		$template = $this->find_template( 'admin-template-options' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the styling options area of the email editor
	 *
	 * @since 1.0.0
	 */
	public function print_styling_options() {
		
		$template = $this->find_template( 'admin-styling-options' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the styling options area of the email editor
	 *
	 * @since 1.0.0
	 */
	public function print_section_editor() {
		
		$template = $this->find_template( 'admin-section-editor' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the email preview area of the email editor
	 *
	 * @since 1.0.0
	 */
	public function print_email_preview() {
		
		$template = $this->find_template( 'admin-email-preview' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Get the current content for the email, if it exists
	 * @since 1.0.0
	 */
	public function get_email_options() {

		$this->content 					= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Mail_Content', true ) : '';
		$this->plain_text_content 		= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Plain_Text_Mail_Content', true ) : '';

		$this->content_alignment 		= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Content_Alignment', true ) : '';
		$this->max_width 				= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Max_Width', true ) : '';
		$this->background_color 		= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Email_Background_Color', true ) : '';
		$this->body_background_color 	= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Body_Background_Color', true ) : '';
		$this->block_background_color 	= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Block_Background_Color', true ) : '';
		$this->block_border 			= ! empty( $this->post->ID ) ? get_post_meta( $this->post->ID, 'EWD_UWPM_Block_Border', true ) : '';
	}

	/**
	 * Enqueue the necessary CSS and JS files
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		global $ewd_uwpm_controller;

		wp_enqueue_style( 'ewd-uwpm-admin-css', EWD_UWPM_PLUGIN_URL . '/assets/css/ewd-uwpm-admin.css', array(), EWD_UWPM_VERSION );

		wp_enqueue_script( 'ewd-uwpm-admin-js', EWD_UWPM_PLUGIN_URL . '/assets/js/ewd-uwpm-admin.js', array( 'jquery', 'jquery-ui-sortable' ), EWD_UWPM_VERSION, true );
	}
}
