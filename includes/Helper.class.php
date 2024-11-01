<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmHelper' ) ) {
/**
 * Class to to provide helper functions
 *
 * @since 1.0.13
 */
class ewduwpmHelper {

  // Hold the class instance.
  private static $instance = null;

  /**
   * The constructor is private
   * to prevent initiation with outer code.
   * 
   **/
  private function __construct() {}

  /**
   * The object is created from within the class itself
   * only if the class has no instance.
   */
  public static function getInstance() {

    if ( self::$instance == null ) {

      self::$instance = new ewduwpmHelper();
    }
 
    return self::$instance;
  }

  /**
   * Handle ajax requests in admin area for logged out users
   * @since 1.0.13
   */
  public static function admin_nopriv_ajax() {

    wp_send_json_error(
      array(
        'error' => 'loggedout',
        'msg'   => sprintf( __( 'You have been logged out. Please %slogin again%s.', 'ultimate-wp-mail' ), '<a href="' . wp_login_url( admin_url( 'admin.php?page=ewd-uwpm-dashboard' ) ) . '">', '</a>' ),
      )
    );
  }

  /**
   * Handle ajax requests where an invalid nonce is passed with the request
   * @since 1.0.13
   */
  public static function bad_nonce_ajax() {

    wp_send_json_error(
      array(
        'error' => 'badnonce',
        'msg'   => __( 'The request has been rejected because it does not appear to have come from this site.', 'ultimate-wp-mail' ),
      )
    );
  }
}

}