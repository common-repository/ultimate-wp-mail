<?php
class UWPM_Element {

	// the code-friendly name of the element
	public $slug;

	// the display name for the element
	public $label;

	// the section this element should be displayed in
	public $section;

	// the function used to process the element when an email is sent
	public $callback_function;

	// optional additional attributes for the element
	public $attributes;

	// optional styling added to the output
	public $styling_options;
	
	public function __construct( $element_type, $params = array() ) {

		$this->slug = $element_type;

		$this->set_props( $params );
	}

	/**
	 * Set the key properties for this element type
	 * @since  0.0.1
	 */
	public function set_props( $params ) {

		$defaults = array(
			'label' 			=> $this->slug,
			'section' 			=> 'uncategorized',
			'callback_function' => 'print_r',
			'attributes' 		=> array(),
			'styling_options' 	=> array()
		);

		$props = array_merge( $defaults, $params );

		$this->label 				= $props['label'];
		$this->section 				= $props['section'];
		$this->callback_function 	= $props['callback_function'];
		$this->attributes 			= $props['attributes'];
		$this->styling_options 		= $props['styling_options'];
	}
}

?>