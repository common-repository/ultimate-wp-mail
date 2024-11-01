<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmLogging' ) ) {
/**
 * Class to log emails that are sent using the wp_mail function
 *
 * @since 1.1.0
 */
class ewduwpmLogging {

  public function __construct() {

    add_action( 'wp_mail_succeeded',  array( $this, 'log_wp_mail_success' ) );
    add_action( 'wp_mail_failed',     array( $this, 'log_wp_mail_error' ) );

    add_action( 'publish_uwpm_email_log', array( $this, 'maybe_delete_oldest_email_log' ) );
  }

  /**
   * Saves information about an email that was successfully sent
   * @since 1.1.0
   */
  public function log_wp_mail_success( $mail_data ) {
    
    $args = array(
      'post_type'     => EWD_UWPM_EMAIL_LOG_POST_TYPE,
      'post_title'    => $mail_data['subject'],
      'post_content'  => $mail_data['message'],
      'post_status'   => 'publish',
    );

    $post_id = wp_insert_post( $args );

    if ( ! $post_id ) { return; }

    update_post_meta( $post_id, 'status', 'Success' );
    update_post_meta( $post_id, 'recipient', is_array( $mail_data['to'] ) ? implode( ', ', $mail_data['to'] ) : $mail_data['to'] );
    update_post_meta( $post_id, 'headers', $mail_data['headers'] );
    update_post_meta( $post_id, 'attachments', $mail_data['attachments'] );
  }

  /**
   * Saves information about an email that was unsuccessfully sent
   * @since 1.1.0
   */
  public function log_wp_mail_error( $wp_error ) {

    if ( ! is_wp_error( $wp_error ) ) { return; }
  
    $mail_data = $wp_error->get_error_data();
    
    $args = array(
      'post_type'     => EWD_UWPM_EMAIL_LOG_POST_TYPE,
      'post_title'    => $mail_data['subject'],
      'post_content'  => $mail_data['message'],
      'post_status'   => 'publish',
    );

    $post_id = wp_insert_post( $args );

    if ( ! $post_id ) { return; }

    update_post_meta( $post_id, 'status', $wp_error->get_error_message() );
    update_post_meta( $post_id, 'recipient', is_array( $mail_data['to'] ) ? implode( ', ', $mail_data['to'] ) : $mail_data['to'] );
    update_post_meta( $post_id, 'headers', $mail_data['headers'] );
    update_post_meta( $post_id, 'attachments', $mail_data['attachments'] );
  }

  public function maybe_delete_oldest_email_log( $post_id ) {
    global $ewd_uwpm_controller;

    $current_posts = wp_count_posts( EWD_UWPM_EMAIL_LOG_POST_TYPE );

    if ( $current_posts->publish <= $ewd_uwpm_controller->settings->get_setting( 'maximum-email-logs' ) ) { return; }

    $args = array(
      'posts_per_page'  => 1,
      'post_type'       => EWD_UWPM_EMAIL_LOG_POST_TYPE,
      'order_by'        => 'publish_date',
      'order'           => 'ASC',
      'fields'          => 'ids',
    );

    $posts = get_posts( $args );

    if ( empty( $posts ) ) { return; }

    $delete_post_id = reset( $posts );

    wp_delete_post( $delete_post_id );
  }
}

}