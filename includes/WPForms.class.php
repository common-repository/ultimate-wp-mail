<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewduwpmWPForms' ) ) {
	/**
	 * Class to handle WP Forms integration for Ultimate WP Mail
	 *
	 * @since 1.3.0
	 */
	class ewduwpmWPForms {

		public function __construct() {

			add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );

			add_filter( 'wpforms_builder_settings_sections', array( $this, 'add_settings_panel' ) );
			add_action( 'wpforms_form_settings_panel_content', array( $this, 'add_settings' ) );

			add_action( 'wpforms_process_complete',	array( $this, 'maybe_send_sms_form_submission' ), 10, 3 );
		}

		/**
		 * Enqueues the styles for the WPForms SMS panel
		 * @since 1.3.0
		 */
		public function enqueue_scripts() {

			$current_screen = get_current_screen();
	
			if ( $current_screen->id == 'wpforms_page_wpforms-builder' ) {

				wp_enqueue_style( 'ewd-uwpm-wpforms', EWD_UWPM_PLUGIN_URL . '/assets/css/ewd-uwpm-wpforms.css', array(), EWD_UWPM_VERSION );
			}
		}
	
		/**
		 * Adds an SMS settings panel to the WP Forms admin screen
		 * @since 1.3.0
		 */
		public function add_settings_panel( $panels ) {

			$panels['uwpm'] = esc_html__( 'SMS', 'ultimate-wp-mail' );

			return $panels;

		}

		/**
		 * Adds the settings to enable WP Forms integration
		 * @since 1.3.0
		 */
		public function add_settings( $instance ) { 
			global $ewd_uwpm_controller;

			?>

			<div class="wpforms-panel-content-section wpforms-panel-content-section-uwpm<?php echo empty( $ewd_uwpm_controller->permissions->check_permission( 'sms' ) ) ? ' locked' : ' notlocked' ?>">

				<div class="wpforms-panel-content-section-title">
					<?php esc_html_e( 'Ultimate WP Mail SMS', 'ultimate-wp-mail' ); ?>
				</div>
		
				<?php

				wpforms_panel_field(
					'radio',
					'settings',
					'ewd_uwpm_enabled',
					$instance->form_data,
					esc_html__( 'Send an SMS after submitting this form, or enable it only depending on the value of a specific field.', 'ultimate-wp-mail' ),
					array(
						'options' => array(
							'enabled' => array( 'label' => 'Enable' ),
							'disabled' => array( 'label' => 'Disable' ),
							'specific' => array( 'label' => 'Specific Field' )
						)
					)
				);

				$args = array(
			    	'post_type'     => EWD_UWPM_SMS_POST_TYPE,
			    	'numberposts'   => -1,
			    	'cache_results' => false
			    );
			
			    $sms_posts = get_posts( $args );
			
			    foreach ( $sms_posts as $sms_post ) { 
			
			    	$sms_options[ $sms_post->ID ] = $sms_post->post_title;
			    }

				wpforms_panel_field(
					'select',
					'settings',
					'ewd_uwpm_selected_sms',
					$instance->form_data,
					esc_html__( 'Select which SMS, if any, to send.', 'ultimate-wp-mail' ),
					array(
						'options' => $sms_options,
						'placeholder' => __( '-- Select SMS --', 'ultimate-wp-mail' ),
					)
				);

				wpforms_panel_field(
					'select',
					'settings',
					'ewd_uwpm_phone_field',
					$instance->form_data,
					esc_html__( 'Select the field that contains the phone to send the SMS to.', 'ultimate-wp-mail' ),
					array(
						'field_map' => array(
							'text',
							'phone'
						),
						'placeholder' => __( '-- Select Field --', 'ultimate-wp-mail' ),
					)
				);
		
				wpforms_panel_field(
					'select',
					'settings',
					'ewd_uwpm_selected_field',
					$instance->form_data,
					esc_html__( 'If SMS is set to be triggered only for a specific field value, which field should be the trigger?', 'ultimate-wp-mail' ),
					array(
						'field_map' => array(
							'text',
							'select',
							'radio',
							'checkbox',
							'email',
							'address',
							'url',
							'name',
							'hidden',
							'date-time',
							'phone',
							'number',
						),
						'placeholder' => __( '-- Select Field --', 'ultimate-wp-mail' ),
					)
				);

				wpforms_panel_field(
					'text',
					'settings',
					'ewd_uwpm_field_value',
					$instance->form_data,
					esc_html__( 'If SMS is set to be triggered only for a specific field value, what value(s) should trigger the SMS? Separate accepted values with commas.', 'ultimate-wp-mail' ),
					array(
						'default' => '',
						'placeholder' => __( 'SMS trigger value...', 'ultimate-wp-mail' ),
					)
				);

				?>

				<div class="ewd-uwpm-wpforms-locked">

					<div class="ewd-uwpm-wpforms-section-disabled">

						<img src="<?php echo plugins_url( '../assets/img/options-asset-lock.png', __FILE__ ); ?>" alt="Upgrade to Ultimate">

						<p>
							<?php _e( 'Access this section by upgrading to ultimate', 'ultimate-wp-mail' ); ?>
						</p>

						<a href="https://www.etoilewebdesign.com/license-payment/?Selected=UWPMU&Quantity=12" class="get-ultimate-button" target="_blank">
							<?php _e( 'UPGRADE NOW', 'ultimate-wp-mail' ); ?>
						</a>

					</div>

				</div>
		
			</div>

		<?php }

		/**
		 * Send SMS on form submission, if enabled requirements met and an SMS is selected
		 * @since 1.3.0
		 */
		public function maybe_send_sms_form_submission( $fields, $entry, $form_data ) {
			global $ewd_uwpm_controller;

			if ( empty( $ewd_uwpm_controller->permissions->check_permission( 'sms' ) ) ) { return; }
			
			// Return if no valid SMS selected
			if ( empty( intval( $form_data['settings']['ewd_uwpm_selected_sms'] ) ) or get_post_type( $form_data['settings']['ewd_uwpm_selected_sms'] ) != EWD_UWPM_SMS_POST_TYPE ) { return; }

			// Return if SMS not set to specific field trigger or enabled
			if ( $form_data['settings']['ewd_uwpm_enabled'] != 'specific' and $form_data['settings']['ewd_uwpm_enabled'] != 'enabled' ) { return; }
				
			// Return if specific field trigger doesn't match the correct value
			if ( $form_data['settings']['ewd_uwpm_enabled'] == 'specific' ) {

				$match = false;

				$values = explode( ',', $form_data['settings']['ewd_uwpm_field_value'] );

				foreach ( $fields as $field ) {

					if ( $field['id'] != $form_data['settings']['ewd_uwpm_selected_field'] ) { continue; }

					$match = is_array( $field['value'] ) ? ! empty( array_intersect( $values , $field['value'] ) ) : in_array( $field['value'], $values );
				}

				if ( ! $match ) { return; }
			}

			// Return if phone number field is empty
			if ( empty( $form_data['settings']['ewd_uwpm_phone_field'] ) ) { return; }

			$args = array(
				'phone_number' 	=> isset( $fields[ $form_data['settings']['ewd_uwpm_phone_field'] ] ) ? $fields[ $form_data['settings']['ewd_uwpm_phone_field'] ]['value'] : '',
				'email_id'		=> $form_data['settings']['ewd_uwpm_selected_sms'],
			);
			
			$ewd_uwpm_controller->notifications->send_sms( $args );
		}
	}
}