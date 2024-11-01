<?php
class UWPM_Element_Section {

	// the code-friendly name of the section
	public $slug;

	// the display name for the section
	public $label;
	
	public function __construct( $element_type, $params = array() ) {

		$this->slug = $element_type;
		
		$this->label = empty( $params['label'] ) ? $this->slug : $params['label'];
	}
}

?>