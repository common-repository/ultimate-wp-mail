<?php
/**
 * Class to handle all database interactions for the Ultimate WP Mail plugin
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduwpmDatabaseManager' ) ) {
class ewduwpmDatabaseManager {


	// Array containing the arguments for the query
	public $args = array();

	// table names
	public $send_events_table_name;
	public $open_events_table_name;
	public $links_clicked_events_table_name;
	public $email_only_users_table_name;

	public function __construct() {
		global $wpdb;

		$this->send_events_table_name 			= $wpdb->prefix . 'ewd_uwpm_email_send_events';
		$this->open_events_table_name 			= $wpdb->prefix . 'ewd_uwpm_email_open_events';
		$this->links_clicked_events_table_name 	= $wpdb->prefix . 'ewd_uwpm_email_links_clicked_events';
		$this->email_only_users_table_name 		= $wpdb->prefix . 'ewd_uwpm_email_only_users';

		add_action( 'init', array( $this, 'log_user_last_activity' ) );

		add_action( 'init', array( $this, 'maybe_log_email_interaction' ) );
	} 

	/**
	 * Creates the tables used to store event and user information
	 * @since 1.0.0
	 */
	public function create_tables() {

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = "CREATE TABLE $this->send_events_table_name (
  			Email_Send_ID mediumint(9) NOT NULL AUTO_INCREMENT,
			Email_ID mediumint(9) DEFAULT 0 NOT NULL,
			User_ID mediumint(9) DEFAULT 0 NOT NULL,
			Event_ID mediumint(9) DEFAULT 0 NOT NULL,
    		Email_Unique_Identifier text DEFAULT '' NOT NULL,
			Email_Sent_Datetime datetime DEFAULT '0000-00-00 00:00:00' NULL,
  			UNIQUE KEY id (Email_Send_ID)
    		)
			DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
   		
   		dbDelta( $sql );

    	$sql = "CREATE TABLE $this->open_events_table_name (
  			Email_Open_ID mediumint(9) NOT NULL AUTO_INCREMENT,
			Email_Send_ID mediumint(9) DEFAULT 0 NOT NULL,
			Email_Opened text DEFAULT '' NOT NULL,
			Email_Opened_Datetime datetime DEFAULT '0000-00-00 00:00:00' NULL,
  			UNIQUE KEY id (Email_Open_ID)
    		)
			DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
    	
    	dbDelta( $sql );

    	$sql = "CREATE TABLE $this->links_clicked_events_table_name (
  			Email_Link_Clicked_ID mediumint(9) NOT NULL AUTO_INCREMENT,
			Email_Send_ID mediumint(9) DEFAULT 0 NOT NULL,
			Link_URL text DEFAULT '' NOT NULL,
			Link_Click_Count text DEFAULT '' NOT NULL,
			Link_Clicked_Datetime datetime DEFAULT '0000-00-00 00:00:00' NULL,
  			UNIQUE KEY id (Email_Link_Clicked_ID)
    		)
			DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
    	
    	dbDelta( $sql );

    	$sql = "CREATE TABLE $this->email_only_users_table_name (
    		EOU_ID mediumint(9) NOT NULL AUTO_INCREMENT,
    		Email_Address text DEFAULT '' NOT NULL,
    		EOU_Date_Added datetime DEFAULT '0000-00-00 00:00:00' NULL,
    		UNIQUE KEY id (EOU_ID)
    		)
    		DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
    	
    	dbDelta($sql);
	}

	/**
	 * Return all occurrences of an email being sent that match the arguments received
	 * @since 1.0.0
	 */
	public function get_emails_sent( $args ) {
		global $wpdb;

		$query_sql = "SELECT * FROM $this->send_events_table_name sends WHERE 1=%d";

		$query_args = array( 1 );

		if ( ! empty( $args['email_send_id'] ) ) {

			$query_sql .= " AND Email_Send_ID=%d";
			$query_args[] = intval( $args['email_send_id'] );
		}

		if ( ! empty( $args['email_id'] ) ) {

			$query_sql .= " AND Email_ID=%d";
			$query_args[] = intval( $args['email_id'] );
		}

		if ( ! empty( $args['user_id'] ) ) {

			$query_sql .= " AND User_ID=%d";
			$query_args[] = intval( $args['user_id'] );
		}

		if ( ! empty( $args['after'] ) ) {

			$query_sql .= " AND Email_Sent_Datetime>=%s";
			$query_args[] = $args['after'];
		}

		if ( ! empty( $args['before'] ) ) {

			$query_sql .= " AND Email_Sent_Datetime<=%s";
			$query_args[] = $args['before'];
		}

		if ( ! empty( $args['date'] ) ) {

			$query_sql .= " AND DATE(Email_Sent_Datetime)=%s";
			$query_args[] = $args['date'];
		}

		if ( ! empty( $args['orderby'] ) ) {

			$query_sql .= ' ORDER BY ' . esc_sql( $args['orderby'] ) . ' ' . ( strtolower( $args['order'] ) == 'desc' ? 'DESC' : 'ASC' );
		}

		if ( ! empty( $args['sends_per_page'] ) and $args['sends_per_page'] > 0 ) {

			$query_sql .= ' LIMIT ' . intval( ( $args['paged'] - 1 ) * $args['sends_per_page'] ) . ', ' . intval( $args['sends_per_page'] );
		}

		$db_email_sends = $wpdb->get_results( $wpdb->prepare( $query_sql, $query_args ) );

		return $db_email_sends;
	}

	/**
	 * Return all occurrences of an email being opened that match the arguments received
	 * @since 1.0.0
	 */
	public function get_email_opens( $args ) {
		global $wpdb;

		$query_sql = "SELECT * FROM $this->open_events_table_name opens INNER JOIN $this->send_events_table_name sends ON sends.Email_Send_ID = opens.Email_Send_ID WHERE 1=%d";

		$query_args = array( 1 );

		if ( ! empty( $args['email_send_id'] ) ) {

			$query_sql .= " AND opens.Email_Send_ID=%d";
			$query_args[] = intval( $args['email_send_id'] );
		}

		if ( ! empty( $args['email_id'] ) ) {

			$query_sql .= " AND sends.Email_ID=%d";
			$query_args[] = intval( $args['email_id'] );
		}

		if ( ! empty( $args['user_id'] ) ) {

			$query_sql .= " AND sends.User_ID=%d";
			$query_args[] = intval( $args['user_id'] );
		}

		if ( ! empty( $args['after'] ) ) {

			$query_sql .= " AND opens.Email_Opened_Datetime>=%s";
			$query_args[] = $args['after'];
		}

		if ( ! empty( $args['before'] ) ) {

			$query_sql .= " AND opens.Email_Opened_Datetime<=%s";
			$query_args[] = $args['before'];
		}

		if ( ! empty( $args['date'] ) ) {

			$query_sql .= " AND DATE(opens.Email_Opened_Datetime)=%s";
			$query_args[] = $args['date'];
		}

		if ( ! empty( $args['orderby'] ) ) {

			$query_sql .= ' ORDER BY ' . esc_sql( $args['orderby'] ) . ' ' . ( strtolower( $args['order'] ) == 'desc' ? 'DESC' : 'ASC' );
		}

		if ( ! empty( $args['opens_per_page'] ) and $args['opens_per_page'] > 0 ) {

			$query_sql .= ' LIMIT ' . intval( ( $args['paged'] - 1 ) * $args['opens_per_page'] ) . ', ' . intval( $args['opens_per_page'] );
		}

		$db_emails_opened = $wpdb->get_results( $wpdb->prepare( $query_sql, $query_args ) );

		return $db_emails_opened;
	}

	/**
	 * Return the time that an email was last opened by a particular user
	 * @since 1.0.0
	 */
	public function get_user_last_email_open( $user_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT Email_Opened_Datetime FROM $this->open_events_table_name opens INNER JOIN $this->send_events_table_name sends ON sends.Email_Send_ID = opens.Email_Send_ID WHERE sends.User_ID=%d", $user_id ) );
	}

	/**
	 * Return all occurrences of a link being clicked that match the arguments received
	 * @since 1.0.0
	 */
	public function get_email_links_clicked( $args ) {
		global $wpdb;

		$query_sql = "SELECT * FROM $this->links_clicked_events_table_name clicks INNER JOIN $this->send_events_table_name sends ON sends.Email_Send_ID = clicks.Email_Send_ID WHERE 1=%d";

		$query_args = array( 1 );

		if ( ! empty( $args['email_send_id'] ) ) {

			$query_sql .= " AND clicks.Email_Send_ID=%d";
			$query_args[] = intval( $args['email_send_id'] );
		}

		if ( ! empty( $args['email_id'] ) ) {

			$query_sql .= " AND sends.Email_ID=%d";
			$query_args[] = intval( $args['email_id'] );
		}

		if ( ! empty( $args['user_id'] ) ) {

			$query_sql .= " AND sends.User_ID=%d";
			$query_args[] = intval( $args['user_id'] );
		}

		if ( ! empty( $args['after'] ) ) {

			$query_sql .= " AND clicks.Link_Clicked_Datetime>=%s";
			$query_args[] = $args['after'];
		}

		if ( ! empty( $args['before'] ) ) {

			$query_sql .= " AND clicks.Link_Clicked_Datetime<=%s";
			$query_args[] = $args['before'];
		}

		if ( ! empty( $args['date'] ) ) {

			$query_sql .= " AND DATE(clicks.Link_Clicked_Datetime)=%s";
			$query_args[] = $args['date'];
		}

		if ( ! empty( $args['orderby'] ) ) {

			$query_sql .= ' ORDER BY ' . esc_sql( $args['orderby'] ) . ' ' . ( strtolower( $args['order'] ) == 'desc' ? 'DESC' : 'ASC' );
		}

		if ( ! empty( $args['links_per_page'] ) and $args['links_per_page'] > 0 ) {

			$query_sql .= ' LIMIT ' . intval( ( $args['paged'] - 1 ) * $args['links_per_page'] ) . ', ' . intval( $args['links_per_page'] );
		}

		$db_links_clicked = $wpdb->get_results( $wpdb->prepare( $query_sql, $query_args ) );

		return $db_links_clicked;
	}

	/**
	 * Accepts a send event array, inserts it into the database, and returns the ID of the newly inserted send event
	 * @since 1.0.0
	 */
	public function insert_send_event( $send_event ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		$query_args = array(
			'Email_ID' 					=> ! empty( $send_event['email_id'] ) ? $send_event['email_id'] : 0,
			'User_ID' 					=> ! empty( $send_event['user_id'] ) ? $send_event['user_id'] : 0,
			'Event_ID' 					=> ! empty( $send_event['event_id'] ) ? $send_event['event_id'] : 0,
			'Email_Unique_Identifier' 	=> ! empty( $send_event['unique_identifier'] ) ? $send_event['unique_identifier'] : '',
			'Email_Sent_Datetime' 		=> date( 'Y-m-d H:i:s' ),
		);

		$wpdb->insert(
			$this->send_events_table_name,
			$query_args
		);
		
		return $wpdb->insert_id;
	}

	/**
	 * Accepts an open event array, inserts it into the database, and returns the ID of the newly inserted open event
	 * @since 1.0.0
	 */
	public function insert_open_event( $open_event ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		$query_args = array(
			'Email_Send_ID' 			=> ! empty( $open_event['email_send_id'] ) ? $open_event['email_send_id'] : 0,
			'Email_Opened' 				=> ! empty( $open_event['email_opened'] ) ? $open_event['email_opened'] : '',
			'Email_Opened_Datetime' 	=> date( 'Y-m-d H:i:s' ),
		);

		$wpdb->insert(
			$this->open_events_table_name,
			$query_args
		);
		
		return $wpdb->insert_id;
	}

	/**
	 * Accepts a click event array, inserts it into the database, and returns the ID of the newly inserted click event
	 * @since 1.0.0
	 */
	public function insert_click_event( $click_event ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		$query_args = array(
			'Email_Send_ID' 			=> ! empty( $click_event['email_send_id'] ) ? $click_event['email_send_id'] : 0,
			'Link_URL' 					=> ! empty( $click_event['link_url'] ) ? $click_event['link_url'] : '',
			'Link_Click_Count' 			=> ! empty( $click_event['link_click_count'] ) ? $click_event['link_click_count'] : 0,
			'Link_Clicked_Datetime' 	=> date( 'Y-m-d H:i:s' ),
		);

		$wpdb->insert(
			$this->links_clicked_events_table_name,
			$query_args
		);
		
		return $wpdb->insert_id;
	}

	/**
	 * Accepts an email user array, inserts it into the database, and returns the ID of the newly inserted email user
	 * @since 1.0.0
	 */
	public function insert_email_user( $email_user ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		$query_args = array(
			'Email_Address' 			=> ! empty( $email_user['email_address'] ) ? $email_user['email_address'] : '',
			'EOU_Date_Added' 			=> date( 'Y-m-d H:i:s' ),
		);

		$wpdb->insert(
			$this->email_only_users_table_name,
			$query_args
		);
		
		return $wpdb->insert_id;
	}

	/**
	 * Accepts a send event array, updates it into the database, and returns the ID if successful or false otherwise
	 * @since 1.0.0
	 */
	public function update_send_event( $send_event ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		if ( empty( $send_event['email_send_id'] ) ) { return false; }

		$query_args = array(
			'Email_ID' 					=> ! empty( $send_event['email_id'] ) ? $send_event['email_id'] : 0,
			'User_ID' 					=> ! empty( $send_event['user_id'] ) ? $send_event['user_id'] : 0,
			'Event_ID' 					=> ! empty( $send_event['event_id'] ) ? $send_event['event_id'] : 0,
			'Email_Unique_Identifier' 	=> ! empty( $send_event['unique_identifier'] ) ? $send_event['unique_identifier'] : '',
		);

		$wpdb->update(
			$this->send_events_table_name,
			$query_args,
			array( 'Email_Send_ID' => $send_event['email_send_id'] )
		);

		return $send_event['email_send_id'];
	}

	/**
	 * Accepts an open event array, updates it in the database, and returns the ID if successful or false otherwise
	 * @since 1.0.0
	 */
	public function update_open_event( $open_event ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		if ( empty( $open_event['email_open_id'] ) ) { return false; }

		$query_args = array(
			'Email_Send_ID' 			=> ! empty( $open_event['email_send_id'] ) ? $open_event['email_send_id'] : 0,
			'Email_Opened' 				=> ! empty( $open_event['email_opened'] ) ? $open_event['email_opened'] : '',
		);

		$wpdb->update(
			$this->send_events_table_name,
			$query_args,
			array( 'Email_Open_ID' => $open_event['email_open_id'] )
		);

		return $open_event['email_open_id'];
	}

	/**
	 * Accepts a click event array, updates it in the database, and returns the ID if successful or false otherwise
	 * @since 1.0.0
	 */
	public function update_click_event( $click_event ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		if ( empty( $click_event['email_link_clicked_id'] ) ) { return false; }

		$query_args = array(
			'Email_Send_ID' 			=> ! empty( $click_event['email_send_id'] ) ? $click_event['email_send_id'] : 0,
			'Link_URL'	 				=> ! empty( $click_event['link_url'] ) ? $click_event['link_url'] : '',
			'Link_Click_Count'			=> ! empty( $click_event['link_click_count'] ) ? $click_event['link_click_count'] : 0,
		);

		$wpdb->update(
			$this->send_events_table_name,
			$query_args,
			array( 'Email_Link_Clicked_ID' => $click_event['email_link_clicked_id'] )
		);

		return $click_event['email_link_clicked_id'];
	}

	/**
	 * Accepts an email user array, updates it in the database, and returns the ID if successful or false otherwise
	 * @since 1.0.0
	 */
	public function update_email_user( $email_user ) {
		global $wpdb;
		global $ewd_uwpm_controller;

		if ( empty( $email_user['eou_id'] ) ) { return false; }

		$query_args = array(
			'Email_Address' 			=> ! empty( $email_user['email_address'] ) ? $email_user['email_address'] : '',
		);

		$wpdb->update(
			$this->send_events_table_name,
			$query_args,
			array( 'EOU_ID' => $email_user['eou_id'] )
		);

		return $email_user['eou_id'];
	}

	public function delete_user_statistics( $user_id ) {
		global $wpdb;

		$wpdb->get_results( $wpdb->prepare( "DELETE $this->open_events_table_name FROM $this->open_events_table_name INNER JOIN $this->send_events_table_name sends ON sends.Email_Send_ID = $this->open_events_table_name.Email_Send_ID WHERE sends.User_ID=%d", $user_id ) );

		$wpdb->get_results( $wpdb->prepare( "DELETE $this->links_clicked_events_table_name FROM $this->links_clicked_events_table_name INNER JOIN $this->send_events_table_name sends ON sends.Email_Send_ID = $this->links_clicked_events_table_name.Email_Send_ID WHERE sends.User_ID=%d", $user_id ) );

		$wpdb->get_results( $wpdb->prepare( "DELETE FROM $this->send_events_table_name WHERE User_ID=%d", $user_id ) );
	}

	/**
	 * Return the max Event ID + 1 from the send events table
	 * @since 1.0.0
	 */
	public function get_next_send_event_id() {
		global $wpdb;

		return $wpdb->get_var( "SELECT MAX( Event_ID ) FROM $this->send_events_table_name" ) + 1;
	}

	/**
	 * Return all users who haven't logged in since a certain time
	 * @since 1.0.0
	 */
	public function get_users_without_login( $cutoff_time ) {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT user_id FROM $wpdb->usermeta 
			WHERE $wpdb->usermeta.meta_key = 'EWD_UWPM_User_Last_Activity'
			AND $wpdb->usermeta.meta_value < $cutoff_time
			AND user_id IN (
				SELECT user_id FROM $wpdb->usermeta
				WHERE ($wpdb->usermeta.meta_key = 'EWD_UWPM_Login_Reminder_Sent'
				AND $wpdb->usermeta.meta_value != 'Yes')
			)"
		);
	}

	/**
	 * Return all users who have abandoned there WooCommerce carts since a certain time
	 * @since 1.0.0
	 */
	public function get_cart_abandoned_users( $cutoff_time ) {
		global $wpdb;

		return $wpdb->get_results(
			"SELECT user_id FROM $wpdb->usermeta 
			WHERE $wpdb->usermeta.meta_key = 'EWD_UWPM_User_Cart_Update_Time'
			AND $wpdb->usermeta.meta_value < $cutoff_time
			AND user_id IN (
				SELECT user_id FROM $wpdb->usermeta
				WHERE $wpdb->usermeta.meta_key = 'EWD_UWPM_Abandoned_Cart_Reminder_Sent'
				AND $wpdb->usermeta.meta_value != 'Yes'
			)"
		);
	}

	/**
	 * Record the most recent activity time for logged in users
	 * @since 1.0.0
	 */
	public function log_user_last_activity() {

		if ( ! get_current_user_id() ) { return; }

		update_user_meta( get_current_user_id(), 'EWD_UWPM_User_Last_Activity', time() );
		update_user_meta( get_current_user_id(), 'EWD_UWPM_Login_Reminder_Sent', 'No' );
	}

	/**
	 * Record if a site visit represents an email open or an email link being clicked
	 * @since 1.0.0
	 */
	public function maybe_log_email_interaction() {
		global $ewd_uwpm_controller;

		if ( empty( $_GET['ewd_uwpm_id'] ) ) { return; }

		if ( empty( $_GET['ewd_upwm_link_url'] ) ) { 
			
			$this->track_opens(); 
		}
		else { 
			
			$this->track_clicks(); 
		}

		add_action( 'shutdown', 'ewd_uwpm_end_clean', 1 );
	}

	/**
	 * Record an email open, if it's the first time an email is opened
	 * @since 1.0.0
	 */

	public function track_opens() {
		global $wpdb;

		header( 'Content-Type: image/png' );

		readfile( EWD_UWPM_PLUGIN_DIR . '/assets/img/transparent.png' );
	
		$email_unique_identifier = sanitize_text_field( $_GET['ewd_uwpm_id'] );

		$email_send_id = $wpdb->get_var( $wpdb->prepare( "SELECT Email_Send_ID FROM $this->send_events_table_name WHERE Email_Unique_Identifier=%s", $email_unique_identifier ) );
	
		if ( $email_send_id == 0 ) { return; }

		$wpdb->get_row( $wpdb->prepare( "SELECT Email_Send_ID FROM $this->open_events_table_name WHERE Email_Send_ID=%d", $email_send_id ) );

		if ($wpdb->num_rows == 0) {

			$email_opened_datetime = date( 'Y-m-d H:i:s' );

			$wpdb->insert(
				$this->open_events_table_name ,
				array(
					'Email_Send_ID' => $email_send_id,
					'Email_Opened' => 'Yes',
					'Email_Opened_Datetime' => $email_opened_datetime
				)
			);
		}

		die();
	}

	/**
	 * Record an email link click
	 * @since 1.0.0
	 */

	public function track_clicks() {
		global $wpdb;
	
		$email_unique_identifier = sanitize_text_field( $_GET['ewd_uwpm_id'] );

		$link_url = sanitize_url( $_GET['ewd_upwm_link_url'] );

		$email_send_id = $wpdb->get_var( $wpdb->prepare( "SELECT Email_Send_ID FROM $this->send_events_table_name WHERE Email_Unique_Identifier=%s", $email_unique_identifier ) );
	
		if ( $email_send_id == 0 ) { 

			header( 'location:' . $link_url ); 
		}

		$links_clicked = $wpdb->get_row( $wpdb->prepare( "SELECT Email_Send_ID, Link_Click_Count FROM $this->links_clicked_events_table_name WHERE Email_Send_ID=%d", $email_send_id ) );

		if ( $wpdb->num_rows == 0 ) {

			$link_clicked_datetime = date( 'Y-m-d H:i:s' );
	
			$wpdb->insert(
				$this->links_clicked_events_table_name,
				array(
					'Email_Send_ID' 		=> $email_send_id,
					'Link_URL' 				=> $link_url,
					'Link_Click_Count' 		=> 1,
					'Link_Clicked_Datetime' => $link_clicked_datetime
				)
			);
		}
		else {
			$wpdb->update(
				$this->links_clicked_events_table_name,
				array(
					'Link_Click_Count' 		=> $links_clicked->Link_Click_Count + 1
				),
				array(
					'Email_Link_Clicked_ID' => $links_clicked->Email_Link_Clicked_ID
				)
			);
		}
	
		header( 'location:' . $link_url );
	}
}
}