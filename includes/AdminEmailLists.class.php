<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmAdminEmailLists' ) ) {
/**
 * Class to handle the admin email lists page for Ultimate WP Mail
 *
 * @since 1.0.0
 */
class ewduwpmAdminEmailLists {

	public function __construct() {

		// Add the admin menu
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 12 );

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
			_x( 'Lists', 'Title of admin page that lets you view and edit all email lists', 'ultimate-wp-mail' ),
			_x( 'Lists', 'Title of the email lists admin menu item', 'ultimate-wp-mail' ), 
			$ewd_uwpm_controller->settings->get_setting( 'access-role' ), 
			'ewd-uwpm-email-lists', 
			array( $this, 'show_admin_email_lists_page' )
		);
	}

	/**
	 * Display the admin email lists page
	 * @since 1.0.0
	 */
	public function show_admin_email_lists_page() {

		if ( ! empty( $_POST['ewd-uwpm-email-lists-submit'] ) ) {

			$this->save_email_lists();
		}

		$email_lists = is_array( get_option( 'ewd-uwpm-email-lists' ) ) ? get_option( 'ewd-uwpm-email-lists' ) : array();

		$email_lists[] = (object) array(
			'id'				=> 0,
			'name'				=> 'New List',
			'emails_sent'		=> 0,
			'last_send_date'	=> 'N/A',
			'user_list'			=> array(),
		);

		?>

		<div class="wrap">
			<h1>
				<?php _e( 'Email Lists', 'ultimate-wp-mail' ); ?>
			</h1>

			<?php do_action( 'ewd_uwpm_email_lists_table_top' ); ?>

			<form id="ewd-uwpm-email-lists-table" method="POST" action="">

				<div class="ewd-uwpm-list-explanation">
					<?php _e( 'Create lists of WordPress users that you can send specific emails to.', 'ultimate-wp-mail' ); ?><br/>
					<?php _e( 'There are also default lists that you can email, depending on the integrations you have turned on (ex. all users who previously made a purchase through WooCommerce)', 'ultimate-wp-mail' ); ?>
				</div>

				<div id='ewd-uwpm-email-lists-table-div'>

					<input type='hidden' name='ewd_uwpm_email_list_save_values' />

					<div class='ewd-uwpm-email-list-heading-row'>
						<div class='ewd-uwpm-email-list-heading-cell'><?php _e( 'List Name', 'ultimate-wp-mail' ); ?></div>
						<div class='ewd-uwpm-email-list-heading-cell'><?php _e( 'Number of Users', 'ultimate-wp-mail' ); ?></div>
						<div class='ewd-uwpm-email-list-heading-cell'><?php _e( 'Emails Sent', 'ultimate-wp-mail' ); ?></div>
						<div class='ewd-uwpm-email-list-heading-cell'><?php _e( 'Last Email Sent', 'ultimate-wp-mail' ); ?></div>
						<div class='ewd-uwpm-email-list-heading-cell'></div>
					</div>

					<?php foreach ( $email_lists as $email_list ) { ?>

						<div class='ewd-uwpm-email-list <?php echo ( empty( $email_list->id ) ? 'ewd-uwpm-hidden ewd-uwpm-email-list-template' : '' ); ?>'>
							<input type='hidden' name='ewd_uwpm_email_list_id' value='<?php echo esc_attr( $email_list->id ); ?>' />
							<input type='hidden' name='ewd_uwpm_email_list_user_list' value='<?php echo json_encode( $email_list->user_list ); ?>' />

							<div class='ewd-uwpm-email-list-cell ewd-uwpm-email-list-details'>
								<label><?php _e( 'List Name', 'ultimate-wp-mail' ); ?></label>
								<input type='hidden' name='ewd_uwpm_email_list_name' value='<?php echo esc_attr( $email_list->name ); ?>' />
								<span class='ewd_uwpm_email_list_name'> <?php echo esc_html( $email_list->name ); ?></span>
							</div>

							<div class='ewd-uwpm-email-list-cell'>
								<label><?php _e( 'Number of Users', 'ultimate-wp-mail' ); ?></label>
								<span class='ewd_uwpm_email_list_user_count'> <?php echo ( is_array( $email_list->user_list ) ?  sizeOf( $email_list->user_list ) : '0' ); ?></span>
							</div>

							<div class='ewd-uwpm-email-list-cell'>
								<label><?php _e( 'Emails Sent', 'ultimate-wp-mail' ); ?></label>
								<input type='hidden' name='ewd_uwpm_email_list_emails_sent' value='<?php echo esc_attr( $email_list->emails_sent ); ?>' />
								<span class='ewd_uwpm_email_list_emails_sent'> <?php echo $email_list->emails_sent ? esc_html( $email_list->emails_sent ) : 0; ?></span>
							</div>

							<div class='ewd-uwpm-email-list-cell'>
								<label><?php _e( 'Last Send Date', 'ultimate-wp-mail' ); ?></label>
								<input type='hidden' name='ewd_uwpm_email_list_last_send_date' value='<?php echo esc_attr( $email_list->last_send_date ); ?>' />
								<span class='ewd_uwpm_email_list_last_send_date'><?php echo $email_list->last_send_date ? esc_html( $email_list->last_send_date ) : 'N/A'; ?></span>
							</div>

							<div class='ewd-uwpm-email-list-cell ewd-uwpm-email-list-delete'>
								<?php _e( 'Delete', 'ultimate-wp-mail' ); ?>
							</div>

						</div>

					<?php } ?>

					<div class='ewd-uwpm-email-lists-add'>
						<?php _e( '+ ADD', 'ultimate-wp-mail' ); ?>
					</div>

				</div>

				<input type='submit' class='button button-primary' name='ewd-uwpm-email-lists-submit' value='<?php _e( 'Update Email Lists', 'ultimate-wp-mail' ); ?>' />
				
			</form>
			<?php do_action( 'ewd_uwpm_email_lists_table_bottom' ); ?>

			<div class='ewd-uwpm-list-background ewd-uwpm-hidden'></div>
			
			<div class='ewd-uwpm-edit-list ewd-uwpm-hidden' data-currentid='0'>
				
				<div class='ewd-uwpm-close-list-edit'><?php _e( 'Close', 'order-tracking' ); ?></div>
				
				<div class='ewd-uwpm-edit-list-name'>
					<?php _e( 'List name: ', 'ultimate-wp-mail' ); ?>
					<input type='text' class='ewd-uwpm-list-name' />
				</div>

				<div class="ewd-uwpm-edit-list-inside">

					<div class='ewd-uwpm-edit-list-users-div'>

						<h3><?php _e( 'Add Users', 'ultimate-wp-mail' ); ?></h3>

						<div class='ewd-uwpm-add-select-all-users'>
							<?php _e( 'Select All', 'ultimate-wp-mail' ); ?>
						</div>

						<div class='ewd-uwpm-all-users-table'>

							<?php foreach ( get_users() as $user ) { ?>

								<div class='ewd-uwpm-user-entry'>
									<input type='checkbox' name='add_users[]' class='ewd-uwpm-add-user-id' value='<?php echo $user->ID; ?>' data-name='<?php echo esc_attr( $user->display_name ); ?>' />
									<span class='ewd-uwpm-user-display-name'><?php echo esc_html( $user->display_name ); ?></span>
								</div>

							<?php } ?>

						</div>

						<button class='ewd-uwpm-add-list-users ewd-uwpm-button'><?php _e( 'Add Users', 'ultimate-wp-mail' ); ?></button>

					</div>

					<div class='ewd-uwpm-edit-list-users-div'>

						<h3><?php _e( 'Current Users', 'ultimate-wp-mail' ); ?></h3>

						<div class='ewd-uwpm-current-users-table'></div>

						<button class='ewd-uwpm-remove-list-users ewd-uwpm-button'><?php _e( 'Remove Users', 'ultimate-wp-mail' ); ?></button>

					</div>

				</div> <!-- ewd-uwpm-edit-list-inside -->

				<div class='ewd-uwpm-clear'></div>

				<div class='ewd-uwpm-save-list-edit ewd-uwpm-button'><?php _e( 'Save', 'ultimate-wp-mail' ); ?></div>

			</div>

		</div>

		<?php
	}

	/**
	 * Save the custom fields when the form is submitted
	 * @since 1.0.0
	 */
	public function save_email_lists() {

		$email_lists = json_decode( stripslashes( sanitize_text_field( $_POST['ewd_uwpm_email_list_save_values'] ) ) );

		if ( ! empty( $email_lists ) ) {

			foreach ( $email_lists as $key => $email_list ) {

				$email_list->user_list = json_decode( $email_list->user_list );

				$email_lists[ $key ] = $email_list;
			}
		}

		$email_lists = empty( $email_lists ) ? array() : $email_lists;

		update_option( 'ewd-uwpm-email-lists', $email_lists );
	}

	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( $screen->id == 'orders_page_ewd-uwpm-email-lists' ) {

			wp_enqueue_style( 'ewd-uwpm-admin-css', EWD_UWPM_PLUGIN_URL . '/assets/css/ewd-uwpm-admin.css', array(), EWD_UWPM_VERSION );
			wp_enqueue_script( 'ewd-uwpm-admin-js', EWD_UWPM_PLUGIN_URL . '/assets/js/ewd-uwpm-admin.js', array( 'jquery', 'jquery-ui-sortable' ), EWD_UWPM_VERSION, true );
		}
	}
}
} // endif;
