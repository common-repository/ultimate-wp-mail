<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewduwpmBlocks' ) ) {
/**
 * Class to handle plugin Gutenberg blocks
 *
 * @since 1.0.0
 */
class ewduwpmBlocks {

	public function __construct() {

		add_action( 'init', array( $this, 'add_blocks' ) );
		
		add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );
	}

	/**
	 * Add the Gutenberg block to the list of available blocks
	 * @since 1.0.0
	 */
	public function add_blocks() {

		if ( ! function_exists( 'render_block_core_block' ) ) { return; }

		$this->enqueue_assets();   

		$args = array(
			'attributes' => array(
				'display_interests' => array(
					'type' => 'string',
				),
			),
			'editor_script'   	=> 'ewd-uwpm-blocks-js',
			'editor_style'  	=> 'ewd-uwpm-blocks-css',
			'render_callback' 	=> 'ewd_uwpm_subscription_interests_shortcode',
		);

		register_block_type( 'ultimate-wp-mail/ewd-uwpm-subscription-interests-block', $args );
	}

	/**
	 * Create a new category of blocks to hold our block
	 * @since 1.0.0
	 */
	public function add_block_category( $categories ) {
		
		$categories[] = array(
			'slug'  => 'ewd-uwpm-blocks',
			'title' => __( 'Ultimate WP Mail', 'ultimate-wp-mail' ),
		);

		return $categories;
	}

	/**
	 * Register the necessary JS and CSS to display the block in the editor
	 * @since 1.0.0
	 */
	public function enqueue_assets() {

		wp_register_script( 'ewd-uwpm-blocks-js', EWD_UWPM_PLUGIN_URL . '/assets/js/ewd-uwpm-blocks.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), EWD_UWPM_VERSION );
		wp_register_style( 'ewd-uwpm-blocks-css', EWD_UWPM_PLUGIN_URL . '/assets/css/ewd-uwpm-blocks.css', array( 'wp-edit-blocks' ), EWD_UWPM_VERSION );
	}
}

}