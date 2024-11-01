<?php
/**
 * Class to create the 'About Us' submenu
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduwpmAboutUs' ) ) {
class ewduwpmAboutUs {

	public function __construct() {

		add_action( 'wp_ajax_ewd_uwpm_send_feature_suggestion', array( $this, 'send_feature_suggestion' ) );

		add_action( 'admin_menu', array( $this, 'register_menu_screen' ) );
	}

	/**
	 * Adds About Us submenu page
	 * @since 1.1.0
	 */
	public function register_menu_screen() {
		global $ewd_uwpm_controller;

		add_submenu_page(
			'edit.php?post_type=uwpm_mail_template', 
			esc_html__( 'About Us', 'ultimate-wp-mail' ),
			esc_html__( 'About Us', 'ultimate-wp-mail' ),
			$ewd_uwpm_controller->settings->get_setting( 'access-role' ),
			'ewd-uwpm-about-us',
			array( $this, 'display_admin_screen' )
		);
	}

	/**
	 * Displays the About Us page
	 * @since 1.1.0
	 */
	public function display_admin_screen() { ?>

		<div class='ewd-uwpm-about-us-logo'>
			<img src='<?php echo plugins_url( "../assets/img/ewd_new_logo_purple2.png", __FILE__ ); ?>'>
		</div>

		<div class='ewd-uwpm-about-us-tabs'>

			<ul id='ewd-uwpm-about-us-tabs-menu'>

				<li class='ewd-uwpm-about-us-tab-menu-item ewd-uwpm-tab-selected' data-tab='who_we_are'>
					<?php _e( 'Who We Are', 'ultimate-wp-mail' ); ?>
				</li>

				<li class='ewd-uwpm-about-us-tab-menu-item' data-tab='lite_vs_premium'>
					<?php _e( 'Ultimate Version', 'ultimate-wp-mail' ); ?>
				</li>

				<li class='ewd-uwpm-about-us-tab-menu-item' data-tab='getting_started'>
					<?php _e( 'Getting Started', 'ultimate-wp-mail' ); ?>
				</li>

				<li class='ewd-uwpm-about-us-tab-menu-item' data-tab='suggest_feature'>
					<?php _e( 'Suggest a Feature', 'ultimate-wp-mail' ); ?>
				</li>

			</ul>

			<div class='ewd-uwpm-about-us-tab' data-tab='who_we_are'>

				<p>
					<strong>Founded in 2014, Etoile Web Design is a leading WordPress plugin development company. </strong>
					Privately owned and located in Canada, our growing business is expanding in size and scope. 
					We have more than 50,000 active users across the world, over 2,000,000 total downloads, and our client based is steadily increasing every day. 
					Our reliable WordPress plugins bring a tremendous amount of value to our users by offering them solutions that are designed to be simple to maintain and easy to use. 
					Our plugins, like the <a href='https://www.etoilewebdesign.com/plugins/ultimate-product-catalog/?utm_source=admin_about_us' target='_blank'>Ultimate Product Catalog</a>, <a href='https://www.etoilewebdesign.com/plugins/ultimate-wp-mail/?utm_source=admin_about_us' target='_blank'>Order Status Tracking</a>, <a href='https://www.etoilewebdesign.com/plugins/ultimate-faq/?utm_source=admin_about_us' target='_blank'>Ultimate FAQs</a> and <a href='https://www.etoilewebdesign.com/plugins/ultimate-reviews/?utm_source=admin_about_us' target='_blank'>Ultimate Reviews</a> are rich in features, highly customizable and responsive. 
					We provide expert support to all of our customers and believe in being a part of their success stories.
				</p>

				<p>
					Our current team consists of web developers, marketing associates, digital designers and product support associates. 
					As a small business, we are able to offer our team flexible work schedules, significant autonomy and a challenging environment where creative people can flourish.
				</p>

			</div>

			<div class='ewd-uwpm-about-us-tab ewd-uwpm-hidden' data-tab='lite_vs_premium'>

				<p><?php _e( 'The ultimate version of the plugin adds the ability to send SMS notifications and includes WPForms integration, so you can trigger SMS notifications for form submissions. With this you can:', 'ultimate-wp-mail' ); ?></p>

				<ul>
					<li><?php _e( 'Create unlimited SMS messages.', 'ultimate-wp-mail' ); ?></li>
					<li><?php _e( 'Trigger SMS notifications via send events (e.g. site update, user profile update, new post, etc.).', 'ultimate-wp-mail' ); ?></li>
					<li><?php _e( 'Trigger SMS notifications for WooCommerce events (e.g. new purchase, purchase follow-up, order update, review request, new product listed, etc.).', 'ultimate-wp-mail' ); ?></li>
					<li><?php _e( 'Add SMS notification functionality to WPForms, so you can trigger an SMS notification to the user on form submission and even make it dependent on a specific input value in the form.', 'ultimate-wp-mail' ); ?></li>
					<li><?php _e( 'Send SMS notifications to a user-created list.', 'ultimate-wp-mail' ); ?></li>
					<li><?php _e( 'Send SMS notifications to an automatically-created list.', 'ultimate-wp-mail' ); ?></li>
					<li><?php _e( 'Send SMS notifications to an individual user or to all users.', 'ultimate-wp-mail' ); ?></li>
				</ul>

				<?php printf( __( '<a href="%s" target="_blank" class="ewd-uwpm-about-us-tab-button ewd-uwpm-about-us-tab-button-purchase">Buy Ultimate Version</a>', 'ultimate-wp-mail' ), 'https://www.etoilewebdesign.com/license-payment/?Selected=UWPMU&Quantity=12&utm_source=admin_about_us' ); ?>
				
			</div>

			<div class='ewd-uwpm-about-us-tab ewd-uwpm-hidden' data-tab='getting_started'>

				<p><?php _e( 'If you wish to re-run the intro video that played when you first activated the plugin, and explained how to get started setting up your emails and subscription interests, just click the button below.', 'ultimate-wp-mail' ); ?></p>

				<?php printf( __( '<a href="%s" class="ewd-uwpm-about-us-tab-button ewd-uwpm-about-us-tab-button-walkthrough">Re-Run Getting Started Video</a>', 'ultimate-wp-mail' ), admin_url( '?page=ewd-uwpm-getting-started' ) ); ?>

				<p><?php _e( 'To view our video playlist for this plugin, please click the button below.', 'ultimate-wp-mail' ); ?></p>

				<?php printf( __( '<a href="%s" target="_blank" class="ewd-uwpm-about-us-tab-button ewd-uwpm-about-us-tab-button-youtube">YouTube Playlist</a>', 'ultimate-wp-mail' ), 'https://www.youtube.com/playlist?list=PLEndQUuhlvSpry0rmQLP2IajiJteUjwYl' ); ?>

				
			</div>

			<div class='ewd-uwpm-about-us-tab ewd-uwpm-hidden' data-tab='suggest_feature'>

				<div class='ewd-uwpm-about-us-feature-suggestion'>

					<p><?php _e( 'You can use the form below to let us know about a feature suggestion you might have.', 'ultimate-wp-mail' ); ?></p>

					<textarea placeholder="<?php _e( 'Please describe your feature idea...', 'ultimate-wp-mail' ); ?>"></textarea>
					
					<br>
					
					<input type="email" name="feature_suggestion_email_address" placeholder="<?php _e( 'Email Address', 'ultimate-wp-mail' ); ?>">
				
				</div>
				
				<div class='ewd-uwpm-about-us-tab-button ewd-uwpm-about-us-send-feature-suggestion'>Send Feature Suggestion</div>
				
			</div>

		</div>

	<?php }

	/**
	 * Sends the feature suggestions submitted via the About Us page
	 * @since 1.1.0
	 */
	public function send_feature_suggestion() {
		global $ewd_uwpm_controller;
		
		if (
			! check_ajax_referer( 'ewd-uwpm-admin-js', 'nonce' ) 
			|| 
			! current_user_can( $ewd_uwpm_controller->settings->get_setting( 'access-role' ) )
		) {
			ewduwpmHelper::admin_nopriv_ajax();
		}

		$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";  
	    $feedback = sanitize_text_field( $_POST['feature_suggestion'] );
		$feedback .= '<br /><br />Email Address: ';
	  	$feedback .=  sanitize_email( $_POST['email_address'] );
	
	  	wp_mail( 'contact@etoilewebdesign.com', 'UWPM Feature Suggestion', $feedback, $headers );
	
	  	die();
	} 

}
} // endif;