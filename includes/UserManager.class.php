<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduwpmUserManager' ) ) {
/**
 * Class to handle subscription/unsubscription of users
 *
 * @since 1.0.0
 */
class ewduwpmUserManager {

	public function __construct() {

		add_action( 'register_form', 			array( $this, 'add_registration_checkboxes' ), 99 );
		add_action( 'user_register', 			array( $this, 'save_registration_checkboxes' ) );

		add_action( 'personal_options', 		array( $this, 'edit_profile_checkboxes' ) ); 
		add_action( 'personal_options_update', 	array( $this, 'save_edit_profile_checkboxes' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_edit_profile_checkboxes' ) );
		
		add_filter( 'the_content', 				array( $this, 'append_subscription_interests' ) );
		add_action( 'the_content',			    array( $this, 'process_unsubscribe_link_click' ) );

		add_filter( 'user_contactmethods',		array( $this, 'maybe_add_phone_to_contact_methods' ) );
	}

	/**
	 * Add subscribe/unsubscribe checkboxes to the registration form, if selected
	 * @since 1.0.0
	 */
	public function add_registration_checkboxes() {
		global $ewd_uwpm_controller;

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-subscribe-checkbox' ) ) { 

			?>

			<style>.login input[type=checkbox] {width:auto;}</style>
			<label for="ewd_uwpm_subscribe"><?php _e( 'Subscribe to Update Notifications', 'ultimate-wp-mail' ) ?><br />
        	<input type="checkbox" name="ewd_uwpm_subscribe" id="ewd_uwpm_subscribe" class="input" value="<?php echo esc_attr( wp_unslash( $ewd_uwpm_subscribe ) ); ?>" size="25" /></label>
			
			<?php 
		}

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-unsubscribe-checkbox' ) ) { 

			?>

			<style>.login input[type=checkbox] {width:auto;}</style>
			<label for="ewd_uwpm_unsubscribe"><?php _e( 'Unsubscribe from Update Notifications', 'ultimate-wp-mail' ) ?><br />
        	<input type="checkbox" name="ewd_uwpm_unsubscribe" id="ewd_uwpm_unsubscribe" class="input" value="<?php echo esc_attr( wp_unslash( $ewd_uwpm_unsubscribe ) ); ?>" size="25" /></label>
			
			<?php 
		}
	}

	/**
	 * Save the selections for subscribe/unsubscribe checkboxes, if enabled
	 * @since 1.0.0
	 */
	public function save_registration_checkboxes( $user_id ) {
		global $ewd_uwpm_controller;

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-subscribe-checkbox' ) ) {

			if ( isset( $_POST['ewd_uwpm_subscribe'] ) ) { update_user_meta( $user_id, 'EWD_UWPM_User_Subscribe', 'Yes' ); }
			else { update_user_meta( $user_id, 'EWD_UWPM_User_Subscribe', 'No' ); }
		}

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-unsubscribe-checkbox' ) ) {

			if ( isset( $_POST['ewd_uwpm_unsubscribe'] ) ) { update_user_meta( $user_id, 'EWD_UWPM_User_Unsubscribe', 'Yes' ); }
			else { update_user_meta( $user_id, 'EWD_UWPM_User_Unsubscribe', 'No' ); }
		}
	}

	/**
	 * Add subscribe/unsubscribe checkboxes to the edit profile form, if selected
	 * @since 1.0.0
	 */
	public function edit_profile_checkboxes( $user ) {
		global $ewd_uwpm_controller;

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-subscribe-checkbox' ) ) {

			?>

			<tr class="show-admin-bar user-admin-bar-front-wrap">
				<th scope="row"><?php _e( 'Subscribe to Updates', 'ultimate-wp-mail' ); ?></th>
				<td>
					<fieldset>
						<label for="ewd_uwpm_subscribe">
							<input name="ewd_uwpm_subscribe" type="checkbox" id="ewd_uwpm_subscribe" value="1" <?php echo ( get_user_meta( $user->ID, 'EWD_UWPM_User_Subscribe', true) == "Yes" ) ? 'checked' : ''; ?> />
						</label>
					</fieldset>
				</td>
			</tr>

			<?php
		}

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-unsubscribe-checkbox' ) ) {

			?>

			<tr class="show-admin-bar user-admin-bar-front-wrap">
				<th scope="row"><?php _e( 'Unsubscribe from Updates', 'ultimate-wp-mail' ); ?></th>
				<td>
					<fieldset>
						<label for="ewd_uwpm_subscribe">
							<input name="ewd_uwpm_unsubscribe" type="checkbox" id="ewd_uwpm_unsubscribe" value="1" <?php echo ( get_user_meta( $user->ID, 'EWD_UWPM_User_Unsubscribe', true ) == "Yes" ) ? 'checked' : ''; ?> />
						</label>
					</fieldset>
				</td>
			</tr>

			<?php
		}
	}

	/**
	 * Save the selections for subscribe/unsubscribe checkboxes, if enabled
	 * @since 1.0.0
	 */
	public function save_edit_profile_checkboxes( $user_id ) {
		global $ewd_uwpm_controller;

		if ( ! current_user_can( 'edit_user', $user_id ) ) { 

    	    return false; 
    	}

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-subscribe-checkbox' ) ) {

			if ( isset( $_POST['ewd_uwpm_subscribe'] ) ) { update_user_meta( $user_id, 'EWD_UWPM_User_Subscribe', 'Yes' ); }
			else { update_user_meta( $user_id, 'EWD_UWPM_User_Subscribe', 'No' ); }
		}

		if ( $ewd_uwpm_controller->settings->get_setting( 'add-unsubscribe-checkbox' ) ) {

			if ( isset( $_POST['ewd_uwpm_unsubscribe'] ) ) { update_user_meta( $user_id, 'EWD_UWPM_User_Unsubscribe', 'Yes' ); }
			else { update_user_meta( $user_id, 'EWD_UWPM_User_Unsubscribe', 'No' ); }
		}
	}

	/**
	 * Sets a user to 'unsubscribed' when they click on the unsubscribe email link
	 * @since 1.0.0
	 */
	public function process_unsubscribe_link_click( $content ) {
		global $ewd_uwpm_controller;

	   if ( empty( $_GET['action'] ) or $_GET['action'] != 'ewd_uwpm_unsubscribe' ) { return $content; }

	   $user_id = intval( substr( $_GET['code'], 0, strpos( $_GET['code'], 'pl' ) ) );

	   if ( empty( $user_id ) ) { return $content; }

	   $user = get_user_by( 'id', $user_id );

	   if ( $user->user_email != $_GET['email'] ) { return $content; }

	   update_user_meta( $user->ID, 'EWD_UWPM_User_Unsubscribe', 'Yes' );

	   if( ! empty( $ewd_uwpm_controller->settings->get_setting( 'unsubscribe-redirect-url' ) ) ) {
	    wp_redirect( $ewd_uwpm_controller->settings->get_setting( 'unsubscribe-redirect-url' ) );
	    exit(0);
	   }
	   else {
	   	return '<p>' . __( 'You have been successully unsubscribed.', 'ultimate-wp-mail' ) . '</p>' . $content;
	   }
	}

	/**
	 * Adds the post interest checkboxes to the top/bottom of a post, if selected
	 * @since 1.0.0
	 */
	public function append_subscription_interests( $content ) {
		global $ewd_uwpm_controller;
	
		$post = get_post();

		if ( empty( $post ) or $post->post_type != 'post' ) { return $content; }
	
		$categories = wp_get_post_categories( $post->ID );

		$content = $ewd_uwpm_controller->settings->get_setting( 'display-post-interests' ) == 'before' ? do_shortcode( '[subscription-interests display_interests="post_categories" post_categories="' . implode( ',', $categories ) . '"]' ) . $content : $content;

		$content = $ewd_uwpm_controller->settings->get_setting( 'display-post-interests' ) == 'after' ? $content . do_shortcode( '[subscription-interests display_interests="post_categories" post_categories="' . implode( ',', $categories ) . '"]' ) : $content;
	
		return $content;
	}

	public function maybe_add_phone_to_contact_methods( $methods ) {
		global $ewd_uwpm_controller;

		if ( empty( $ewd_uwpm_controller->permissions->check_permission( 'sms' ) ) ) { return $methods; }

		return array_merge( array( 'ewd_uwpm_phone_number' => __( 'Phone Number', 'ultimate-wp-mail' ) ), $methods );
	}
}
}