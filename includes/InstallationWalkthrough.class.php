<?php

/**
 * Class to handle everything related to the walk-through that runs on plugin activation
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

class ewduwpmInstallationWalkthrough {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_install_screen' ) );
		add_action( 'admin_head', array( $this, 'hide_install_screen_menu_item' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );

		add_action( 'admin_head', array( $this, 'admin_enqueue' ) );
	}

	/**
	 * On activation, redirect the user if they haven't used the plugin before
	 * @since 1.0.0
	 */
	public function redirect() {
		global $ewd_uwpm_controller;

		if ( ! get_transient( 'ewd-uwpm-getting-started' ) ) 
			return;

		delete_transient( 'ewd-uwpm-getting-started' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		if ( ! empty( get_posts( array( 'post_type' => EWD_UWPM_EMAIL_POST_TYPE ) ) ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=ewd-uwpm-getting-started' ) );
		exit;
	}

	/**
	 * Create the installation admin page
	 * @since 1.0.0
	 */
	public function register_install_screen() {

		add_dashboard_page(
			esc_html__( 'Ultimate WP Mail - Welcome!', 'ultimate-wp-mail' ),
			esc_html__( 'Ultimate WP Mail - Welcome!', 'ultimate-wp-mail' ),
			'manage_options',
			'ewd-uwpm-getting-started',
			array($this, 'display_install_screen')
		);
	}

	/**
	 * Hide the installation admin page from the WordPress sidebar menu
	 * @since 1.0.0
	 */
	public function hide_install_screen_menu_item() {

		remove_submenu_page( 'index.php', 'ewd-uwpm-getting-started' );
	}

	/**
	 * Enqueue the admin assets necessary to run the walk-through and display it nicely
	 * @since 1.0.0
	 */
	public function admin_enqueue() {

		if ( ! isset( $_GET['page'] ) or $_GET['page'] != 'ewd-uwpm-getting-started' ) { return; }

		wp_enqueue_style( 'ewd-uwpm-admin-css', EWD_UWPM_PLUGIN_URL . '/assets/css/admin.css', array(), EWD_UWPM_VERSION );
		wp_enqueue_style( 'ewd-uwpm-welcome-screen', EWD_UWPM_PLUGIN_URL . '/assets/css/ewd-uwpm-welcome-screen.css', array(), EWD_UWPM_VERSION );
		
		wp_enqueue_script( 'ewd-uwpm-getting-started', EWD_UWPM_PLUGIN_URL . '/assets/js/ewd-uwpm-welcome-screen.js', array( 'jquery' ), EWD_UWPM_VERSION );
	}

	/**
	 * Output the HTML of the walk-through screen
	 * @since 1.0.0
	 */
	public function display_install_screen() { 

	?>

		<div class='ewd-uwpm-welcome-screen'>
			
			<div class='ewd-uwpm-welcome-screen-header'>
				<h1><?php _e('Welcome to Ultimate WP Mail', 'ultimate-wp-mail'); ?></h1>
				<p><?php _e('Thanks for choosing Ultimate WP Mail! The following will show you how you can use the plugin to create emails, send them based on user interests or interactions, and track email sends, opens and links clicked.', 'ultimate-wp-mail'); ?></p>
			</div>

			<div class='ewd-uwpm-welcome-screen-box ewd-uwpm-welcome-screen-statuses ewd-uwpm-welcome-screen-open' data-screen='statuses'>
				<h2><?php _e('Getting Started', 'ultimate-faqs'); ?></h2>
				<div class='ewd-uwpm-welcome-screen-box-content'>
					<iframe class="ewd-uwpm-welcome-screen-video" width="560" height="315" src="https://www.youtube.com/embed/x1fqMIg71Ik" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>

			<div class='ewd-uwpm-welcome-screen-skip-container'>
				<a href='admin.php?page=ewd-uwpm-settings'><div class='ewd-uwpm-welcome-screen-skip-button'><?php _e( 'Go to Settings', 'ultimate-wp-mail' ); ?></div></a>
			</div>
		</div>

	<?php }
}


?>