<?php
/**
 * Allow third-party plugins to send emails
 * @since 1.0.0
 */
function ewd_uwpm_send_email( $args ) {
	global $ewd_uwpm_controller;

	if ( ! is_array( $args ) ) { return false; }

	$lowercase_args = array_change_key_case( $args );
	$args = $lowercase_args;

	if ( ! empty( $args['email_current_user'] ) and get_current_user_id() ) {

		$args['user_id'] = get_current_user_id();

		return $ewd_uwpm_controller->notifications->email_user( $args );
	}
	elseif ( ! empty( $args['user_id'] ) ) {

		return $ewd_uwpm_controller->notifications->email_user( $args );
	}
	elseif ( ! empty( $args['email_address'] ) and ! empty( $args['email_id'] ) ) {

		$email = get_post( $args['email_id'] );

		$args['email_title'] = $email->post_title;
		$args['email_content'] = get_post_meta( $email->ID, 'EWD_UWPM_Mail_Content', true );

		return $ewd_uwpm_controller->notifications->send_email( $args );
	}

	return false;
}

/**
 * Create a shortcode to display a subscription interest form
 * @since 1.0.0
 */
function ewd_uwpm_subscription_interests_shortcode( $atts ) {
	global $ewd_uwpm_controller;

	// Define shortcode attributes
	$interest_atts = array(
		'display_interests' 		=> '',
		'post_categories' 			=> '',
		'uwpm_categories' 			=> '',
		'woocommerce_categories' 	=> '',
		'display_login_message' 	=> 'yes'
	);

	// Create filter so addons can modify the accepted attributes
	$interest_atts = apply_filters( 'ewd_uwpm_tracking_form_shortcode_atts', $interest_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $interest_atts, $atts );

	// Render booking form
	ewd_uwpm_load_view_files();

	$interest_form = new ewduwpmViewSubscriptionInterest( $args );

	$output = $interest_form->render();

	return $output;
}
add_shortcode( 'subscription-interests', 'ewd_uwpm_subscription_interests_shortcode' );

/**
 * Adds in a function that allows custom element sections to be registered
 * @deprecated
 * @since 1.0.0
 */
function uwpm_register_custom_element_section( $section_type, $params = array() ) {
	global $ewd_uwpm_controller;
  
    // Sanitize post type name
    $section_type = sanitize_key( $section_type );
 
    if ( empty( $section_type ) || strlen( $section_type ) > 40 ) {

        return __( 'Section name must be between 1 and 30 characters', 'ultimate-wp-mail' );
    }
 
    $custom_element_section = new UWPM_Element_Section( $section_type, $params );
 
    return $ewd_uwpm_controller->custom_element_manager->register_custom_element_section( $custom_element_section );
}

/**
 * Adds in a function that allows custom elements to be registered
 * @deprecated
 * @since 1.0.0
 */
function uwpm_register_custom_element( $element_type, $params = array() ) {
    global $ewd_uwpm_controller;
 
    // Sanitize post type name
    $element_type = sanitize_key( $element_type );
 
    if ( empty( $element_type ) || strlen( $element_type ) > 40 ) {

        return __( 'Custom element name must be between 1 and 40 characters', 'ultimate-wp-mail' );
    }
 
    $custom_element = new UWPM_Element( $element_type, $params );
 
    return $ewd_uwpm_controller->custom_element_manager->register_custom_element( $custom_element );
}

function ewd_uwpm_load_view_files() {

	$files = array(
		EWD_UWPM_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
	);

	$files = apply_filters( 'ewd_uwpm_load_view_files', $files );

	foreach( $files as $file ) {
		require_once( $file );
	}
}

function ewd_uwpm_add_ob_start() {

    ob_start();
}
add_action( 'init', 'ewd_uwpm_add_ob_start', 1 );

// Calls during normal site loads (ie: non-email related loads)
function ewd_uwpm_end_flush() {

	if ( ob_get_level() > 0 ) {

		ob_end_flush();
	}
}
add_action( 'shutdown', 'ewd_uwpm_end_flush' );

// Called if a site visit represents an email open or link click
function ewd_uwpm_end_clean() {

	if ( ob_get_level() > 0 ) {

		ob_end_clean();
	}
}

if ( ! function_exists( 'ewd_uwpm_decode_infinite_table_setting' ) ) {
function ewd_uwpm_decode_infinite_table_setting( $values ) {
	
	return is_array( json_decode( html_entity_decode( $values ) ) ) ? json_decode( html_entity_decode( $values ) ) : array();
}
}

if ( ! function_exists( 'ewd_hex_to_rgb' ) ) {
function ewd_hex_to_rgb( $hex ) {

	$hex = str_replace("#", "", $hex);

	// return if the string isn't a color code
	if ( strlen( $hex ) !== 3 and strlen( $hex ) !== 6 ) { return '0,0,0'; }

	if(strlen($hex) == 3) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}

	$rgb = $r . ", " . $g . ", " . $b;
  
	return $rgb;
}
}

if ( ! function_exists( 'ewd_format_classes' ) ) {
function ewd_format_classes( $classes ) {

	if ( count( $classes ) ) {
		return ' class="' . join( ' ', $classes ) . '"';
	}
}
}

if ( ! function_exists( 'ewd_add_frontend_ajax_url' ) ) {
function ewd_add_frontend_ajax_url() { ?>
    
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
<?php }
}

if ( ! function_exists( 'ewd_random_string' ) ) {
function ewd_random_string( $length = 10 ) {

	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';

    for ( $i = 0; $i < $length; $i++ ) {

        $randstring .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
    }

    return $randstring;
}
}