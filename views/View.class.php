<?php

/**
 * Base class for any view requested on the front end.
 *
 * @since 1.0.0
 */
class ewduwpmView extends ewduwpmBase {

	/**
	 * Post type to render
	 */
	public $post_type = null;

	/**
	 * Map types of content to the template which will render them
	 */
	public $content_map = array(
		'title'							 => 'content/title',
	);

	// Locations that should be searched for templates
	public $template_dirs;

	// Default labels, used a fallbacks if no admin inputted label exists
	public $label_defaults = array();

	// The classes that should be added to the main form div
	public $classes;

	/**
	 * Initialize the class
	 * @since 1.0.0
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
		
		// Filter the content map so addons can customize what and how content
		// is output. Filters are specific to each view, so for this base view
		// you would use the filter 'us_content_map_ewduwpmView'
		$this->content_map = apply_filters( 'ewd_uwpm_content_map_' . get_class( $this ), $this->content_map );

	}

	/**
	 * Render the view and enqueue required stylesheets
	 *
	 * @note This function should always be overridden by an extending class
	 * @since 1.0.0
	 */
	public function render() {

		$this->set_error(
			array( 
				'type'		=> 'render() called on wrong class'
			)
		);
	}

	/**
	 * Load a template file for views
	 *
	 * First, it looks in the current theme's /ewd-uwpm-templates/ directory. Then it
	 * will check a parent theme's /ewd-uwpm-templates/ directory. If nothing is found
	 * there, it will retrieve the template from the plugin directory.

	 * @since 1.0.0
	 * @param string template Type of template to load (eg - reviews, review)
	 */
	function find_template( $template ) {

		$this->template_dirs = array(
			get_stylesheet_directory() . '/' . EWD_UWPM_TEMPLATE_DIR . '/',
			get_template_directory() . '/' . EWD_UWPM_TEMPLATE_DIR . '/',
			EWD_UWPM_PLUGIN_DIR . '/' . EWD_UWPM_TEMPLATE_DIR . '/'
		);
		
		$this->template_dirs = apply_filters( 'ewd_uwpm_template_directories', $this->template_dirs );

		foreach ( $this->template_dirs as $dir ) {
			if ( file_exists( $dir . $template . '.php' ) ) {
				return $dir . $template . '.php';
			}
		}

		return false;
	}

	/**
	 * Enqueue stylesheets
	 */
	public function enqueue_assets() {

		//enqueue assets here
	}

	/**
	 * Prints an action notification, if any action has happened
	 *
	 * @since 1.0.0
	 */
	public function maybe_print_update_message() {
		
		if ( empty( $this->update_message ) ) { return; }
		
		$template = $this->find_template( 'update-message' );
		
		if ( $template ) {
			include( $template );
		}
	}

	public function get_option( $option_name ) {
		global $ewd_uwpm_controller;

		return ! empty( $this->$option_name ) ? $this->$option_name : $ewd_uwpm_controller->settings->get_setting( $option_name );
	}

	public function get_label( $label_name ) {
		global $ewd_uwpm_controller;

		if ( empty( $this->label_defaults ) ) { $this->set_label_defaults(); }

		return ! empty( $ewd_uwpm_controller->settings->get_setting( $label_name ) ) ? $ewd_uwpm_controller->settings->get_setting( $label_name ) : $this->label_defaults[ $label_name ];
	}

	public function set_label_defaults() {

		$this->label_defaults = array(
			'label-subscribe'					=> __( 'Subscribe', 'ultimate-wp-mail' ),
			'label-unsubscribe'					=> __( 'Unsubscribe', 'ultimate-wp-mail' ),
			'label-login-select-topics'			=> __( 'Log in to your account so that you can subscribe to topics you\'re interested in!', 'ultimate-wp-mail' ),
			'label-select-topics'				=> __( 'Select topics you\'re interested in below to receive emails when new items are posted!', 'ultimate-wp-mail' ),			
		);
	}

	public function add_custom_styling() {
		global $ewd_uwpm_controller;

		echo '<style>';
			if ( $ewd_uwpm_controller->settings->get_setting( 'styling-toggle-background-color' ) != '' ) { echo '.ewd-uwpm-post-margin-symbol { background-color: ' . $ewd_uwpm_controller->settings->get_setting( 'styling-toggle-background-color' ) . ' !important; }'; }

			echo $ewd_uwpm_controller->settings->get_setting( 'custom-css' );

		echo  '</style>';
	}

}
