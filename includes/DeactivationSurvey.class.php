<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduwpmDeactivationSurvey' ) ) {
/**
 * Class to handle plugin deactivation survey
 *
 * @since 1.0.0
 */
class ewduwpmDeactivationSurvey {

	public function __construct() {
		add_action( 'current_screen', array( $this, 'maybe_add_survey' ) );
	}

	public function maybe_add_survey() {
		if ( in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ), true) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_deactivation_scripts') );
			add_action( 'admin_footer', array( $this, 'add_deactivation_html') );
		}
	}

	public function enqueue_deactivation_scripts() {
		wp_enqueue_style( 'ewd-uwpm-deactivation-css', EWD_UWPM_PLUGIN_URL . '/assets/css/plugin-deactivation.css' );
		wp_enqueue_script( 'ewd-uwpm-deactivation-js', EWD_UWPM_PLUGIN_URL . '/assets/js/plugin-deactivation.js', array( 'jquery' ) );

		wp_localize_script( 'ewd-uwpm-deactivation-js', 'ewd_uwpm_deactivation_data', array( 'site_url' => site_url() ) );
	}

	public function add_deactivation_html() {
		
		$install_time = get_option( 'ewd-uwpm-installation-time' );

		$options = array(
			1 => array(
				'title'   => esc_html__( 'I no longer need the plugin', 'ultimate-wp-mail' ),
			),
			2 => array(
				'title'   => esc_html__( 'I\'m switching to a different plugin', 'ultimate-wp-mail' ),
				'details' => esc_html__( 'Please share which plugin', 'ultimate-wp-mail' ),
			),
			3 => array(
				'title'   => esc_html__( 'I couldn\'t get the plugin to work', 'ultimate-wp-mail' ),
				'details' => esc_html__( 'Please share what wasn\'t working', 'ultimate-wp-mail' ),
			),
			4 => array(
				'title'   => esc_html__( 'It\'s a temporary deactivation', 'ultimate-wp-mail' ),
			),
			5 => array(
				'title'   => esc_html__( 'Other', 'ultimate-wp-mail' ),
				'details' => esc_html__( 'Please share the reason', 'ultimate-wp-mail' ),
			),
		);
		?>
		<div class="ewd-uwpm-deactivate-survey-modal" id="ewd-uwpm-deactivate-survey-ultimate-wp-mail">
			<div class="ewd-uwpm-deactivate-survey-wrap">
				<form class="ewd-uwpm-deactivate-survey" method="post" data-installtime="<?php echo $install_time; ?>">
					<span class="ewd-uwpm-deactivate-survey-title"><span class="dashicons dashicons-testimonial"></span><?php echo ' ' . __( 'Quick Feedback', 'ultimate-wp-mail' ); ?></span>
					<span class="ewd-uwpm-deactivate-survey-desc"><?php echo __('If you have a moment, please share why you are deactivating Ultimate WP Mail:', 'ultimate-wp-mail' ); ?></span>
					<div class="ewd-uwpm-deactivate-survey-options">
						<?php foreach ( $options as $id => $option ) : ?>
							<div class="ewd-uwpm-deactivate-survey-option">
								<label for="ewd-uwpm-deactivate-survey-option-ultimate-wp-mail-<?php echo $id; ?>" class="ewd-uwpm-deactivate-survey-option-label">
									<input id="ewd-uwpm-deactivate-survey-option-ultimate-wp-mail-<?php echo $id; ?>" class="ewd-uwpm-deactivate-survey-option-input" type="radio" name="code" value="<?php echo $id; ?>" />
									<span class="ewd-uwpm-deactivate-survey-option-reason"><?php echo $option['title']; ?></span>
								</label>
								<?php if ( ! empty( $option['details'] ) ) : ?>
									<input class="ewd-uwpm-deactivate-survey-option-details" type="text" placeholder="<?php echo $option['details']; ?>" />
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="ewd-uwpm-deactivate-survey-footer">
						<button type="submit" class="ewd-uwpm-deactivate-survey-submit button button-primary button-large"><?php _e('Submit and Deactivate', 'ultimate-wp-mail' ); ?></button>
						<a href="#" class="ewd-uwpm-deactivate-survey-deactivate"><?php _e('Skip and Deactivate', 'ultimate-wp-mail' ); ?></a>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}

}