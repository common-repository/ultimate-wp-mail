<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmAdminUserStats' ) ) {
/**
 * Class to handle the admin user stats page for Ultimate WP Mail
 *
 * @since 1.0.0
 */
class ewduwpmAdminUserStats {

	// The ewduwpmUserStatsTable being displayed
	public $user_stats_table;

	public function __construct() {

		// Add the admin menu
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 12 );

		// Hide the 'User Stats Details' item from the side menu
		add_action( 'admin_head', array( $this, 'hide_add_new_menu_item' ) );

		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 12 );
	}

	/**
	 * Add the top-level admin menu page
	 * @since 1.0.0
	 */
	public function add_menu_page() {
		global $ewd_uwpm_controller;

		add_submenu_page( 
			'edit.php?post_type=uwpm_mail_template', 
			_x( 'User Stats', 'Title of admin page that lets you view statistics about individual user interactions', 'ultimate-wp-mail' ),
			_x( 'User Stats', 'Title of the user statistics admin menu item', 'ultimate-wp-mail' ), 
			$ewd_uwpm_controller->settings->get_setting( 'access-role' ), 
			'ewd-uwpm-user-stats', 
			array( $this, 'show_admin_user_stats_page' )
		);

		add_submenu_page( 
			'edit.php?post_type=uwpm_mail_template', 
			_x( 'User Stat Details', 'Title of admin page that lets you view statistics about interactions for an individual user', 'ultimate-wp-mail' ),
			_x( 'User Stat Details', 'Title of the user statistic details admin menu item', 'ultimate-wp-mail' ), 
			$ewd_uwpm_controller->settings->get_setting( 'access-role' ), 
			'ewd-uwpm-user-stat-details', 
			array( $this, 'show_admin_user_stat_details_page' )
		);
	}

	/**
	 * Hides the "User Stat Details" page from the admin menus
	 * @since 1.0.0
	 */
	public function hide_add_new_menu_item() {

		remove_submenu_page( 'edit.php?post_type=uwpm_mail_template', 'ewd-uwpm-user-stat-details' );
	}

	/**
	 * Display the admin user stats page
	 * @since 1.0.0
	 */
	public function show_admin_user_stats_page() {

		require_once( EWD_UWPM_PLUGIN_DIR . '/includes/WP_List_Table.UserStatsTable.class.php' );
		$this->user_stats_table = new ewduwpmUserStatsTable();
		$this->user_stats_table->prepare_items();

		?>

		<div class="wrap">
			<h1>
				<?php _e( 'User Statistics', 'ultimate-wp-mail' ); ?>
			</h1>

			<?php do_action( 'ewd_uwpm_user_stats_table_top' ); ?>
			<form id="ewd-uwpm-user-stats-table" method="POST" action="">
				<input type="hidden" name="page" value="ewd-uwpm-user-stats">

				<div class="ewd-uwpm-primary-controls clearfix">
					<div class="ewd-uwpm-views">
						<?php $this->user_stats_table->views(); ?>
					</div>
					<?php $this->user_stats_table->advanced_filters(); ?>
				</div>

				<?php $this->user_stats_table->display(); ?>
			</form>
			<?php do_action( 'ewd_uwpm_orders_table_bottom' ); ?>
		</div>

		<?php
	}

	/**
	 * Display the admin user stat details page
	 * @since 1.0.0
	 */
	public function show_admin_user_stat_details_page() {
		global $ewd_uwpm_controller;

		$args = array(
			'user_id'	=> ! empty( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0,
			'orderby'	=> 'Email_Sent_Datetime',
			'order'		=> 'DESC'
		);

		$user_send_events = $ewd_uwpm_controller->database_manager->get_emails_sent( $args );

		?>

		<div class="wrap">

			<table class="wp-list-table striped widefat tags sorttable fields-list ui-sortable" cellspacing="0">

				<thead>

					<tr>

						<th scope='col' class='manage-column column-type sortable desc'  style="">
							<span><?php _e( 'Email Sent', 'ultimate-wp-mail' ) ?></span>
						</th>

						<th scope='col' id='type' class='manage-column column-type'  style="">
							<span><?php _e( 'Email Opened?', 'ultimate-wp-mail' ) ?></span>
						</th>

						<th scope='col' id='description' class='manage-column column-description'  style="">
							<span><?php _e( 'Links Clicked', 'ultimate-wp-mail' ) ?></span>
						</th>

						<th scope='col' id='required' class='manage-column column-users'  style="">
							<span><?php _e( 'Send Date/Time', 'ultimate-wp-mail' ) ?></span>
						</th>

					</tr>

				</thead>

				<tbody id="the-list" class='list:tag'>

					<?php foreach ( $user_send_events as $user_send_event ) { ?>

						<tr id='list-item-" . $Email->Email_Send_ID . "' class='list-item'>

							<td class='users column-email-title'>
								<?php echo get_the_title( $user_send_event->Email_ID ); ?>
							</td>

							<td class='users column-opened'>
								<?php echo ( ! empty( $email_opened ) ? __( 'Yes', 'ultimate-wp-mail' ) : __( 'No', 'ultimate-wp-mail' ) ); ?>
							</td>

							<?php 

								$args = array(
									'email_send_id'	=> $user_send_event->Email_Send_ID
								);

								$link_clicks = $ewd_uwpm_controller->database_manager->get_email_links_clicked( $args ); 
							?>

							<td class='users column-links-clicked'>
								<?php echo sizeof( $link_clicks ); ?>
							</td>

							<td class='users column-send-date-time'>
								<?php echo $user_send_event->Email_Sent_Datetime; ?>
							</td>

						</tr>

					<?php } ?>

				</tbody>

				<tfoot>

					<tr>

						<th scope='col' class='manage-column column-type sortable desc'  style="">
							<span><?php _e( 'Email Sent', 'ultimate-wp-mail' ) ?></span>
						</th>

						<th scope='col' id='type' class='manage-column column-type'  style="">
							<span><?php _e( 'Email Opened?', 'ultimate-wp-mail' ) ?></span>
						</th>

						<th scope='col' id='description' class='manage-column column-description'  style="">
							<span><?php _e( 'Links Clicked', 'ultimate-wp-mail' ) ?></span>
						</th>

						<th scope='col' id='required' class='manage-column column-users'  style="">
							<span><?php _e( 'Send Date/Time', 'ultimate-wp-mail' ) ?></span>
						</th>

					</tr>

				</tfoot>

			</table>

		</div>

		<?php
	}

	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( $screen->id == 'uwpm_mail_template_page_ewd-uwpm-user-stat-details' ) {

			wp_enqueue_style( 'ewd-uwpm-admin-css', EWD_UWPM_PLUGIN_URL . '/assets/css/ewd-uwpm-admin.css', array(), EWD_UWPM_VERSION );
			wp_enqueue_script( 'ewd-uwpm-admin-js', EWD_UWPM_PLUGIN_URL . '/assets/js/ewd-uwpm-admin.js', array( 'jquery', 'jquery-ui-sortable' ), EWD_UWPM_VERSION, true );
		}
	}
}
} // endif;
