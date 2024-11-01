<?php
/**
 * Class to handle all custom element management for the Ultimate WP Mail plugin
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewduwpmCustomElementManager' ) ) {
class ewduwpmCustomElementManager {

	// Array all registered custom element sections
	public $custom_element_sections = array();

	// Array all registered custom elements
	public $custom_elements = array();

	public function __construct() {
		
		add_action( 'init', array( $this, 'allow_custom_element_registration' ), 11 );
	} 

	/**
	 * Allow third party add-ons to hook in to register their own elements
	 * @since 1.0.0
	 */
	public function allow_custom_element_registration() {

		do_action( 'uwpm_register_custom_element_section' );
		do_action( 'uwpm_register_custom_element' );
	}

	/**
	 * Add a custom element to the registered elements array
	 * @since 1.0.0
	 */
	public function register_custom_element_section( $custom_element_section ) {

		if ( get_class( $custom_element_section ) != 'UWPM_Element_Section' ) { return; }

		$this->custom_element_sections[] = $custom_element_section;
	}

	/**
	 * Add a custom element to the registered elements array
	 * @since 1.0.0
	 */
	public function register_custom_element( $custom_element ) {

		if ( get_class( $custom_element ) != 'UWPM_Element' ) { return; }

		$this->custom_elements[] = $custom_element;
	}

	/**
	 * Return an array of all the registered sections
	 * @since 1.0.0
	 */
	public function get_custom_element_sections() {
    	
    	$sections = array();

    	foreach ( $this->custom_element_sections as $custom_element_section ) {

    	    $section = array(
    	    	'slug' => $custom_element_section->slug, 
    	    	'name' => $custom_element_section->label
    	    );

    	    $sections[] = $section;
    	}

    	return $sections;
	}

	/**
	 * Returns all of the registered elements
	 * @since 1.0.0
	 */
	public function get_custom_elements() {

    	return $this->custom_elements;
	}

	/**
	 * Returns all of the default user fields
	 * @since 1.0.0
	 */
	public function get_user_fields() {

		$contact_methods = wp_get_user_contact_methods();
	
		$user_fields = array(
			array( 'slug' => 'username', 'name' => 'Username' ),
			array( 'slug' => 'fname', 'name' => 'First Name' ),
			array( 'slug' => 'lname', 'name' => 'Last Name' ),
			array( 'slug' => 'nickname', 'name' => 'Nickname' ),
			array( 'slug' => 'dname', 'name' => 'Display Name' ),
			array( 'slug' => 'email', 'name' => 'Email' ),
			array( 'slug' => 'website', 'name' => 'Website' )
		);
	
		foreach ( $contact_methods as $key => $contact_method ) {

			$user_fields[] = array( 'slug' => $key, 'name' => $contact_method );
		}
	
		return $user_fields;
	}

}
}