<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmSMSQueue' ) ) {
/**
 * Class to queue/process emails that are sent using the wp_mail function
 *
 * @since 1.1.0
 */

require_once( EWD_UWPM_PLUGIN_DIR . '/lib/wp-background-processing/wp-async-request.php' );
require_once( EWD_UWPM_PLUGIN_DIR . '/lib/wp-background-processing/wp-background-process.php' );

class ewduwpmSMSQueue extends EWD_WP_Background_Process  {

    /**
     * @var string
     */
    protected $action = 'sms_queue_process';

    /**
     * Task
     *
     * Perform any actions required on each queue item. Return the modified 
     * item for further processing in the next pass through. Or, return false 
     * to remove the item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        global $ewd_uwpm_controller;

        $url = add_query_arg(
            array(
                'plugin'        => 'uwpm',
                'license_key'   => urlencode( get_option( 'uwpm-ultimate-license-key', 'no license key entered' ) ),
                'admin_email'   => urlencode( $ewd_uwpm_controller->settings->get_setting( 'ultimate-purchase-email' ) ),
                'phone_number'  => urlencode( $item['to'] ),
                'message'       => urlencode( $item['message'] ),
                'country_code'  => urlencode( $ewd_uwpm_controller->settings->get_setting( 'sms-country-code' ) )
            ),
            'http://www.etoilewebdesign.com/sms-handling/sms-client.php'
        );

        $opts = array( 'http' =>array( 'method' => "GET" ) );
        $context = stream_context_create( $opts );
        $return = json_decode( file_get_contents( $url, false, $context ) );

        return false;
    }
}

}