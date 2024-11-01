<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduwpmBackwardsCompatibility' ) ) {
/**
 * Class to handle transforming the plugin settings from the 
 * previous style (individual options) to the new one (options array)
 *
 * @since 1.0.0
 */
class ewduwpmBackwardsCompatibility {

	public function __construct() {
		
		if ( empty( get_option( 'ewd-uwpm-settings' ) ) and get_option( 'EWD_UWPM_Full_Version' ) ) { $this->run_backwards_compat(); }
		elseif ( ! get_option( 'ewd-uwpm-permission-level' ) ) { update_option( 'ewd-uwpm-permission-level', 2 ); }
	}

	public function run_backwards_compat() {

		$this->convert_email_lists();

		$settings = array(
			'custom-css' 					=> get_option( 'EWD_UWPM_Custom_CSS' ),
			'track-opens'					=> get_option( 'EWD_UWPM_Track_Opens' ) == 'Yes' ? false : true,
			'track-clicks'					=> get_option( 'EWD_UWPM_Track_Clicks' ) == 'Yes' ? true : false,
			'add-unsubscribe-link'			=> get_option( 'EWD_UWPM_Add_Unsubscribe_Link' ) == 'Yes' ? true : false,
			'unsubscribe-redirect-url'		=> get_option( 'EWD_UWPM_Unsubscribe_Redirect_URL' ),
			'add-subscribe-checkbox'		=> get_option( 'EWD_UWPM_Add_Subscribe_Checkbox' ) == 'Yes' ? true : false,
			'add-unsubscribe-checkbox'		=> get_option( 'EWD_UWPM_Add_Unsubscribe_Checkbox' ) == 'Yes' ? true : false,
			'woocommerce-integration'		=> get_option( 'EWD_UWPM_WooCommerce_Integration' )  ? true : false,
			'display-interests'				=> is_array( get_option( 'EWD_UWPM_Display_Interests' ) ) ? array_map( 'strtolower', get_option( 'EWD_UWPM_Display_Interests' ) ) : array(),
			'display-post-interests'		=> strtolower( get_option( 'EWD_UWPM_Display_Post_Interests' ) ),
			'email-from-name'				=> get_option( 'EWD_UWPM_Email_From_Name' ),
			'email-from-email'				=> get_option( 'EWD_UWPM_Email_From_Email' ),

			'send-actions'					=> $this->convert_send_on_actions(),

			'label-subscribe'				=> get_option( 'EWD_UWPM_Subscribe_Label' ),
			'label-unsubscribe'				=> get_option( 'EWD_UWPM_Unsubscribe_Label' ),
			'label-login-select-topics'		=> get_option( 'EWD_UWPM_Login_To_Select_Topics_Label' ),
			'label-select-topics'			=> get_option( 'EWD_UWPM_Select_Topics_Label' ),
		);

		add_option( 'ewd-uwpm-review-ask-time', get_option( 'EWD_UWPM_Ask_Review_Date' ) );
		add_option( 'ewd-uwpm-installation-time', get_option( 'EWD_UWPM_Install_Time' ) );

		update_option( 'ewd-uwpm-permission-level', get_option( 'EWD_UWPM_Full_Version' ) == 'Yes' ? 2 : 1 );
		
		update_option( 'ewd-uwpm-settings', $settings );
	}

	public function convert_email_lists() {

		$old_email_lists = is_array( get_option( 'EWD_UWPM_Email_Lists_Array' ) ) ? get_option( 'EWD_UWPM_Email_Lists_Array' ) : array();

		$new_email_lists = array();

		foreach ( $old_email_lists as $old_email_list ) {

			$new_email_list = (object) array(
				'id'				=> $old_email_list['ID'],
				'name'				=> $old_email_list['List_Name'],
				'emails_sent'		=> $old_email_list['Emails_Sent'],
				'last_send_date'	=> $old_email_list['Last_Email_Sent_Date'],
				'user_list'			=> $old_email_list['Users']
			);

			$new_email_lists[] = $new_email_list;
		}

		update_option( 'ewd-uwpm-email-lists', $new_email_lists );
	}

	public function convert_send_on_actions() {

		$old_send_on_actions = is_array( get_option( 'EWD_UWPM_Send_On_Actions' ) ) ? get_option( 'EWD_UWPM_Send_On_Actions' ) : array();

		$new_send_on_actions = array();

		foreach ( $old_send_on_actions as $old_send_on_action ) {

			$new_send_on_action = array(
				'id'				=> $old_send_on_action['Send_On_ID'],
				'enabled'			=> $old_send_on_action['Enabled'] == 'Yes' ? true : false,
				'action_type'		=> strtolower( $old_send_on_action['Action_Type'] ),
				'includes'			=> strtolower( $old_send_on_action['Includes'] ),
				'email_id'			=> $old_send_on_action['Email_ID'],
				'interval_count'	=> $old_send_on_action['Interval_Count'],
				'interval_unit'		=> strtolower( $old_send_on_action['Interval_Unit'] ),
			);

			$new_send_on_actions[] = $new_send_on_action;
		}

		return json_encode( $new_send_on_actions );
	}


}

}