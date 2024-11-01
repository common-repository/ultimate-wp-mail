<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( !class_exists( 'ewduwpmUserStatsTable' ) ) {
/**
 * User Stats Table Class
 *
 * Extends WP_List_Table to display the list of user statistics in a format similar to
 * the default WordPress post tables.
 *
 * @h/t Easy Digital Downloads by Pippin: https://easydigitaldownloads.com/
 * @since 1.0.0
 */
class ewduwpmUserStatsTable extends WP_List_Table {

	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $per_page = 30;

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $base_url;

	/**
	 * Current date filters
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $filter_start_date = null;
	public $filter_end_date = null;

	/**
	 * Current time filters
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $filter_start_time = null;
	public $filter_end_time = null;

	/**
	 * Current username filter
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $filter_username;

	/**
	 * Current query string
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $query_string;

	/**
	 * Results of a bulk or quick action
	 *
	 * @var array
	 * @since 1.4.6
	 */
	public $action_result = array();

	/**
	 * Type of bulk or quick action last performed
	 *
	 * @var string
	 * @since 1.4.6
	 */
	public $last_action = '';

	/**
	 * Stored reference to visible columns
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $visible_columns = array();

	/**
	 * Stored reference to extra arguments based in for user stat matching
	 *
	 * @var array
	 * @since 1.0.0
	 */
	public $user_stat_args = array();

	/**
	 * Initialize the table and perform any requested actions
	 *
	 * @since 1.0.0
	 */
	public function __construct( $args = array() ) {

		global $status, $page;

		$this->user_stat_args = $args;

		// Set parent defaults
		parent::__construct( array(
			'singular'  => __( 'User Statistic', 'ultimate-wp-mail' ),
			'plural'    => __( 'User Statistics', 'ultimate-wp-mail' ),
			'ajax'      => false
		) );

		// Set the date filter
		$this->set_date_filter();

		// Strip unwanted query vars from the query string or ensure the correct
		// vars are used
		$this->query_string_maintenance();

		// Run any bulk action requests
		$this->process_bulk_action();

		$this->base_url = admin_url( 'admin.php?page=ewd-uwpm-user-stats' );
	}

	/**
	 * Set the correct date filter
	 *
	 * $_POST values should always overwrite $_GET values
	 *
	 * @since 1.0.0
	 */
	public function set_date_filter( $start_date = null, $end_date = null, $start_time = null, $end_time = null ) {

		if ( !empty( $_GET['action'] ) && $_GET['action'] == 'clear_date_filters' ) {
			$this->filter_start_date 	= null;
			$this->filter_end_date 		= null;
			$this->filter_start_time 	= null;
			$this->filter_end_time 		= null;
		}

		$this->filter_start_date 	= $start_date;
		$this->filter_end_date 		= $end_date;
		$this->filter_start_time 	= $start_time;
		$this->filter_end_time 		= $end_time;

		if ( $start_date === null ) {
			$this->filter_start_date = !empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : null;
			$this->filter_start_date = !empty( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : $this->filter_start_date;
		}

		if ( $end_date === null ) {
			$this->filter_end_date = !empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : null;
			$this->filter_end_date = !empty( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : $this->filter_end_date;
		}

		if ( $start_time === null ) {
			$this->filter_start_time = !empty( $_GET['start_time'] ) ? sanitize_text_field( $_GET['start_time'] ) : null;
			$this->filter_start_time = !empty( $_POST['start_time'] ) ? sanitize_text_field( $_POST['start_time'] ) : $this->filter_start_time;
		}

		if ( $end_time === null ) {
			$this->filter_end_time = !empty( $_GET['end_time'] ) ? sanitize_text_field( $_GET['end_time'] ) : null;
			$this->filter_end_time = !empty( $_POST['end_time'] ) ? sanitize_text_field( $_POST['end_time'] ) : $this->filter_end_time;
		}
	}

	/**
	 * Get the current date range
	 *
	 * @since 1.3
	 */
	public function get_current_date_range() {

		$range = empty( $this->filter_start_date ) ? _x( '*', 'No date limit in a date range, eg 2014-* would mean any date from 2014 or after', 'ultimate-wp-mail' ) : $this->filter_start_date;
		$range .= empty( $this->filter_start_date ) || empty( $this->filter_end_date ) ? '' : _x( '&mdash;', 'Separator between two dates in a date range', 'ultimate-wp-mail' );
		$range .= empty( $this->filter_end_date ) ? _x( '*', 'No date limit in a date range, eg 2014-* would mean any date from 2014 or after', 'ultimate-wp-mail' ) : $this->filter_end_date;

		return $range;
	}

	/**
	 * Strip unwanted query vars from the query string or ensure the correct
	 * vars are passed around and those we don't want to preserve are discarded.
	 *
	 * @since 1.0.0
	 */
	public function query_string_maintenance() {

		$this->query_string = remove_query_arg( array( 'action', 'start_date', 'end_date' ) );

		if ( $this->filter_start_date !== null ) {
			$this->query_string = add_query_arg( array( 'start_date' => $this->filter_start_date ), $this->query_string );
		}

		if ( $this->filter_end_date !== null ) {
			$this->query_string = add_query_arg( array( 'end_date' => $this->filter_end_date ), $this->query_string );
		}

		if ( $this->filter_start_time !== null ) {
			$this->query_string = add_query_arg( array( 'start_time' => $this->filter_start_time ), $this->query_string );
		}

		if ( $this->filter_end_time !== null ) {
			$this->query_string = add_query_arg( array( 'end_time' => $this->filter_end_time ), $this->query_string );
		}

		$this->filter_username = ! isset( $_GET['username'] ) ? '' : sanitize_text_field( $_GET['username'] );
		$this->filter_username = ! isset( $_POST['username'] ) ? $this->filter_username : sanitize_text_field( $_POST['username'] );
		$this->query_string = remove_query_arg( 'username', $this->query_string );
		if ( !empty( $this->filter_username ) ) {
			$this->query_string = add_query_arg( array( 'username' => $this->filter_username ), $this->query_string );
		}
	}

	/**
	 * Show the time views, date filters and the search box
	 * @since 1.0.0
	 */
	public function advanced_filters() {

		// Show the date_range views (today, upcoming, all)
		if ( !empty( $_GET['date_range'] ) ) {
			$date_range = sanitize_text_field( $_GET['date_range'] );
		} else {
			$date_range = '';
		}

		// Use a custom date_range if a date range has been entered
		if ( $this->filter_start_date !== null || $this->filter_end_date !== null ) {
			$date_range = 'custom';
		}

		// Strip out existing date filters from the date_range view urls
		$date_range_query_string = remove_query_arg( array( 'date_range', 'start_date', 'end_date' ), $this->query_string );

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'paged' => FALSE ), remove_query_arg( array( 'date_range' ), $date_range_query_string ) ) ), $date_range === '' ? ' class="current"' : '', __( 'All', 'ultimate-wp-mail' ) ),
			'today'	    => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'date_range' => 'today', 'paged' => FALSE ), $date_range_query_string ) ), $date_range === 'today' ? ' class="current"' : '', __( 'Today', 'ultimate-wp-mail' ) ),
			'past'	    => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array( 'date_range' => 'week', 'paged' => FALSE ), $date_range_query_string ) ), $date_range === 'week' ? ' class="current"' : '', __( 'This Week', 'ultimate-wp-mail' ) ),
		);

		if ( $date_range == 'custom' ) {
			$views['date'] = '<span class="date-filter-range current">' . $this->get_current_date_range() . '</span>';
			$views['date'] .= '<a id="ewd-uwpm-date-filter-link" href="#"><span class="dashicons dashicons-calendar"></span> <span class="ewd-uwpm-date-filter-label">Change date range</span></a>';
		} else {
			$views['date'] = '<a id="ewd-uwpm-date-filter-link" href="#">' . esc_html__( 'Specific Date(s)/Time', 'ultimate-wp-mail' ) . '</a>';
		}

		$views = apply_filters( 'ewd_uwpm_user_stats_table_views_date_range', $views );
		?>

		<div id="ewd-uwpm-filters">
			<ul class="subsubsub ewd-uwpm-views-date_range">
				<li><?php echo join( ' | </li><li>', $views ); ?></li>
			</ul>

			<div class="date-filters">
				<div class="ewd-uwpm-admin-bookings-filters-start">
					<label for="start-date" class="screen-reader-text"><?php _e( 'Start Date:', 'ultimate-wp-mail' ); ?></label>
					<input type="date" id="start-date" name="start_date" class="datepicker" value="<?php echo esc_attr( $this->filter_start_date ); ?>" placeholder="<?php _e( 'Start Date', 'ultimate-wp-mail' ); ?>" />
					<input type="text" id="start-time" name="start_time" class="timepicker" value="<?php echo esc_attr( $this->filter_start_time ); ?>" placeholder="<?php _e( 'Start Time', 'ultimate-wp-mail' ); ?>" />
				</div>
				<div class="ewd-uwpm-admin-bookings-filters-end">
					<label for="end-date" class="screen-reader-text"><?php _e( 'End Date:', 'ultimate-wp-mail' ); ?></label>
					<input type="date" id="end-date" name="end_date" class="datepicker" value="<?php echo esc_attr( $this->filter_end_date ); ?>" placeholder="<?php _e( 'End Date', 'ultimate-wp-mail' ); ?>" />
					<input type="text" id="end-time" name="end_time" class="timepicker" value="<?php echo esc_attr( $this->filter_end_time ); ?>" placeholder="<?php _e( 'Start Time', 'ultimate-wp-mail' ); ?>" />
				</div>
				<input type="submit" class="button button-secondary" value="<?php _e( 'Apply', 'ultimate-wp-mail' ); ?>"/>
				<?php if( !empty( $this->filter_start_date ) || !empty( $this->filter_end_date ) || !empty( $this->filter_start_time ) || !empty( $this->filter_end_time ) ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'clear_date_filters' ) ) ); ?>" class="button button-secondary"><?php _e( 'Clear Filter', 'ultimate-wp-mail' ); ?></a>
				<?php endif; ?>
			</div>

			<?php if( !empty( $_GET['status'] ) ) : ?>
				<input type="hidden" name="status" value="<?php echo esc_attr( sanitize_text_field( $_GET['status'] ) ); ?>"/>
			<?php endif; ?>
		</div>

<?php
	}

	/**
	 * Retrieve the view types
	 * @since 1.0.0
	 */
	public function get_views() {
		global $ewd_uwpm_controller; 

		$current = isset( $_GET['status'] ) ? $_GET['status'] : '';

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( array( 'status', 'paged' ), $this->query_string ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'ultimate-wp-mail' ) . ' <span class="count">(' . sizeof( $this->items ) . ')</span>' ),
		);

		return apply_filters( 'ewd_uwpm_user_stats_table_views_status', $views );
	}

	/**
	 * Generates content for a single row of the table
	 * @since 1.0.0
	 */
	public function single_row( $item ) {
		static $row_alternate_class = '';
		$row_alternate_class = ( $row_alternate_class == '' ? 'alternate' : '' );

		$row_classes = ! empty( $item->post_status ) ? array( esc_attr( $item->post_status ) ) : array();

		if ( !empty( $row_alternate_class ) ) {
			$row_classes[] = $row_alternate_class;
		}

		$row_classes = apply_filters( 'ewd_uwpm_admin_user_stats_list_row_classes', $row_classes, $item );

		echo '<tr class="' . implode( ' ', $row_classes ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Retrieve the table columns
	 *
	 * @since 1.0.0
	 */
	public function get_columns() {
		global $ewd_uwpm_controller;

		// Prevent the lookup from running over and over again on a single
		// page load
		if ( !empty( $this->visible_columns ) ) {
			return $this->visible_columns;
		}

		$all_default_columns = $this->get_all_default_columns();
		$all_columns = $this->get_all_columns();

		$visible_columns = $ewd_uwpm_controller->settings->get_setting( 'user_stats-table-columns' );
		if ( empty( $visible_columns ) ) {
			$columns = $all_default_columns;
		} else {
			$columns = array();
			$columns['cb'] = $all_default_columns['cb'];
			$columns['date'] = $all_default_columns['date'];

			foreach( $all_columns as $key => $column ) {
				if ( in_array( $key, $visible_columns ) ) {
					$columns[$key] = $all_columns[$key];
				}
			}
			$columns['details'] = $all_default_columns['details'];
		}

		$this->visible_columns = apply_filters( 'ewd_uwpm_user_stats_table_columns', $columns );

		return $this->visible_columns;
	}

	/**
	 * Retrieve all default columns
	 *
	 * @since 1.0.0
	 */
	public function get_all_default_columns() {
		global $ewd_uwpm_controller;

		$columns = array(
			'cb'        	=> '<input type="checkbox" />', //Render a checkbox instead of text
			'username'  	=> __( 'Username', 'ultimate-wp-mail' ),
			'emails_sent'  	=> __( 'Emails Sent', 'ultimate-wp-mail' ),
			'emails_opened'	=> __( 'Emails Opened', 'ultimate-wp-mail' ),
			'links_clicked'	=> __( 'Links Clicked', 'ultimate-wp-mail' ),
			'last_opened'	=> __( 'Last Opened Date', 'ultimate-wp-mail' ),
		);

		return $columns;
	}

	/**
	 * Retrieve all available columns
	 *
	 * This is used to get all columns including those deactivated and filtered
	 * out via get_columns().
	 *
	 * @since 1.0.0
	 */
	public function get_all_columns() {
		$columns = $this->get_all_default_columns();

		return apply_filters( 'ewd_uwpm_user_stats_all_table_columns', $columns );
	}

	/**
	 * Retrieve the table's sortable columns
	 * @since 1.0.0
	 */
	public function get_sortable_columns() {
		global $ewd_uwpm_controller;

		$columns = array(
			'username'		=> array( 'number', true ),
		);

		return apply_filters( 'ewd_uwpm_user_stats_table_sortable_columns', $columns );
	}

	/**
	 * This function renders most of the columns in the list table.
	 * @since 1.0.0
	 */
	public function column_default( $user, $column_name ) {
		global $ewd_uwpm_controller;

		switch ( $column_name ) {

			case 'username' :
				
				$value = $user->user_login;

				$value .= '<div class="actions">';
				$value .= '<a href="admin.php?page=ewd-uwpm-user-stat-details&user_id=' . $user->ID . '" data-id="' . esc_attr( $user->ID ) . '">' . __( 'View Details', 'ultimate-wp-mail' ) . '</a>';
				$value .= '</div>';

				break;

			case 'emails_sent' :

				$args = $this->get_user_query_args( $user );

				$value = sizeof( $ewd_uwpm_controller->database_manager->get_emails_sent( $args ) );

				break;

			case 'emails_opened' :

				$args = $this->get_user_query_args( $user );

				$value = sizeof( $ewd_uwpm_controller->database_manager->get_email_opens( $args ) );

				break;

			case 'links_clicked' :

				$args = $this->get_user_query_args( $user );

				$value = sizeof( $ewd_uwpm_controller->database_manager->get_email_links_clicked( $args ) );

				break;

			case 'last_opened' :

				$value = $ewd_uwpm_controller->database_manager->get_user_last_email_open( $user->ID );

				break;

			default:
				$value = '';

				break;

		}

		return apply_filters( 'ewd_uwpm_user_stats_table_column', $value, $user, $column_name );
	}

	public function get_user_query_args( $user ) {

		$args = array(
			'user_id' => $user->ID
		);

		if ( $this->filter_start_date !== null || $this->filter_end_date !== null ) {

			if ( !empty( $this->filter_start_date ) ) {

				$start_date = new DateTime( $this->filter_start_date . ' ' . $this->filter_start_time );
				$args['after'] = $start_date->format( 'Y-m-d H:i:s' );
			}
		
			if ( !empty( $this->filter_end_date ) ) {
			
				$end_date = new DateTime( $this->filter_end_date . ' ' . $this->filter_end_time );
				$args['before'] = $end_date->format( 'Y-m-d H:i:s' );
			}
		}
		elseif ( !empty( $_GET['date_range'] ) ) {

			$args['date_range'] = sanitize_text_field( $_GET['date_range'] );
		}

		return $args;
	}

	/**
	 * Render the checkbox column
	 * @since 1.0.0
	 */
	public function column_cb( $user ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'user_stats',
			$user->ID
		);
	}

	/**
	 * Retrieve the bulk actions
	 * @since 1.0.0
	 */
	public function get_bulk_actions() {
		global $ewd_uwpm_controller;

		$actions = array(
			'reset'   => __( 'Reset Statistics',		'ultimate-wp-mail' ),
		);

		return apply_filters( 'ewd_uwpm_user_stats_table_bulk_actions', $actions );
	}

	/**
	 * Process the bulk actions
	 * @since 1.0.0
	 */
	public function process_bulk_action() {
		global $ewd_uwpm_controller;

		$ids    = isset( $_POST['user_stats'] ) ? $_POST['user_stats'] : false;
		$action = isset( $_POST['action'] ) ? $_POST['action'] : false;

		// Check bulk actions selector below the table
		$action = $action == '-1' && isset( $_POST['action2'] ) ? $_POST['action2'] : $action;

		if( empty( $action ) || $action == '-1' ) {
			return;
		}

		if ( ! current_user_can( $ewd_uwpm_controller->settings->get_setting( 'access-role' ) ) ) {
			return;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$results = array();
		foreach ( $ids as $id ) {

			if ( 'reset' === $action ) {

				$results[$id] = $ewd_uwpm_controller->database_manager->delete_user_statistics( intval( $id ) );
			}

			$results = apply_filters( 'ewd_uwpm_user_stats_table_bulk_action', $results, $id, $action );
		}

		if( count( $results ) ) {
			$this->action_result = $results;
			$this->last_action = $action;
			add_action( 'ewd_uwpm_user_stats_table_top', array( $this, 'admin_notice_bulk_actions' ) );
		}
	}

	/**
	 * Display an admin notice when a bulk action is completed
	 * @since 1.0.0
	 */
	public function admin_notice_bulk_actions() {

		$success = 0;
		$failure = 0;
		foreach( $this->action_result as $id => $result ) {
			if ( $result === true || $result === null ) {
				$success++;
			} else {
				$failure++;
			}
		}

		if ( $success > 0 ) :
		?>

		<div id="ewd-uwpm-admin-notice-bulk-<?php esc_attr( $this->last_action ); ?>" class="updated">

			<?php if ( $this->last_action == 'reset' ) : ?>
			<p><?php echo sprintf( _n( '%d users reset successfully.', '%d user_stats reset successfully.', $success, 'ultimate-wp-mail' ), $success ); ?></p>
			<?php endif; ?>

		</div>

		<?php
		endif;

		if ( $failure > 0 ) :
		?>

		<div id="ewd-uwpm-admin-notice-bulk-<?php esc_attr( $this->last_action ); ?>" class="error">
			<p><?php echo sprintf( _n( '%d users had errors and could not be processed.', '%d users had errors and could not be processed.', $failure, 'ultimate-wp-mail' ), $failure ); ?></p>
		</div>

		<?php
		endif;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * This outputs a separate set of options above and below the table, in
	 * user_stat to make room for the locations, services and providers.
	 *
	 * @since 1.0.0
	 */
	public function display_tablenav( $which ) {
		global $ewd_uwpm_controller;

		// Just call the parent method for the bottom nav
		if ( 'bottom' == $which ) {
			parent::display_tablenav( $which );
			return;
		}
		?>

		<div class="tablenav top ewd-uwpm-top-actions-wrapper">
			<?php wp_nonce_field( 'bulk-' . $this->_args['plural'] ); ?>
			<?php $this->extra_tablenav( $which ); ?>
			<?php parent::pagination( $which ); ?>
		</div>

		<?php $this->add_notification(); ?>

		<div class="ewd-uwpm-table-header-controls">
			<?php if ( $this->has_items() ) : ?>
				<div class="actions bulkactions">
					<?php $this->bulk_actions( $which ); ?>
				</div>
			<?php endif; ?>
			<div class='ewd-uwpm-admin-table-filter-div'>
				<label class='ewd-uwpm-admin-table-filter-label'><?php esc_html_e( 'Username', 'ultimate-wp-mail' ); ?></label>
				<input type='text' class='ewd-uwpm-user_stats-table-filter ewd-uwpm-user_stat-number ewd-uwpm-admin-table-filter-field' name='username' value='<?php echo ( ! empty( $this->filter_username ) ? esc_attr( $this->filter_username ) : '' ); ?>' />
			</div>
		</div>

		<?php
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @param string pos Position of this tablenav: `top` or `btm`
	 * @since 1.4.1
	 */
	public function extra_tablenav( $pos ) {

		do_action( 'ewd_uwpm_user_stats_table_actions', $pos );
	}

	/**
	 * Add notifications above the table to indicate which user_stats are
	 * being shown.
	 * @since 1.3
	 */
	public function add_notification() {

		global $ewd_uwpm_controller;

		$notifications = array();


		if ( !empty( $this->filter_start_date ) || !empty( $this->filter_end_date ) ) {
			$notifications['date'] = sprintf( _x( 'Only user statistics from %s are being shown.', 'Notification of booking date range, eg - user_stats from 2014-12-02-2014-12-05', 'ultimate-wp-mail' ), $this->get_current_date_range() );
		} elseif ( !empty( $_GET['date_range'] ) && $_GET['date_range'] == 'today' ) {
			$notifications['date'] = __( "Only today's user statistics are being shown.", 'ultimate-wp-mail' );
		}

		$notifications = apply_filters( 'ewd_uwpm_admin_user_stats_table_filter_notifications', $notifications );

		if ( !empty( $notifications ) ) :
		?>

			<div class="ewd-uwpm-notice <?php echo esc_attr( $status ); ?>">
				<?php echo join( ' ', $notifications ); ?>
			</div>

		<?php
		endif;
	}

	/**
	 * Setup the final data for the table
	 * @since 1.0.0
	 */
	public function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = array();

		if ( $this->filter_username ) {

			$args['login'] = $this->filter_username;
		}

		$this->items = get_users( $args );

		$total_items   = sizeof( $this->items );

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page )
			)
		);
	}

}
} // endif;
