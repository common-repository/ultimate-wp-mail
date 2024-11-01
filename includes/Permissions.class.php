<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmPermissions' ) ) {
/**
 * Class to handle plugin permissions for Ultimate WP Mail
 *
 * @since 1.2.0
 */
class ewduwpmPermissions {

	private $plugin_permissions;
	private $permission_level;

	public function __construct() {

		$this->plugin_permissions = array(
			'sms' 			=> 3,
			'ultimate' 		=> 3,
		);
	}

	public function set_permissions() {
		
		if ( is_array( get_option( 'ewd-uwpm-permission-level' ) ) ) { return; }

		$this->permission_level = 1;

		update_option( 'ewd-uwpm-permission-level', array( $this->permission_level ) );
	}

	public function get_permission_level() {

		if ( ! is_array( get_option( 'ewd-uwpm-permission-level' ) ) ) { $this->set_permissions(); }

		$permissions_array = get_option( 'ewd-uwpm-permission-level' );

		$this->permission_level = is_array( $permissions_array ) ? reset( $permissions_array ) : $permissions_array;
	}

	public function check_permission( $permission_type = '' ) {

		if ( ! $this->permission_level ) { $this->get_permission_level(); }
		
		return ( array_key_exists( $permission_type, $this->plugin_permissions ) ? ( $this->permission_level >= $this->plugin_permissions[$permission_type] ? true : false ) : false );
	}

	public function update_permissions() {

		$this->permission_level = get_option( 'ewd-uwpm-permission-level' );
	}
}

}