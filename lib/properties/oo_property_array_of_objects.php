<?php

namespace Sunhill\Properties;

class oo_property_array_of_objects extends oo_property {

	protected $type = 'array_of_objects';
	
	protected $features = ['object','complex','array'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	protected function initialize() {
		$this->initialized = true;	
	}
	
	public function set_allowed_objects($object) {
	    $this->validator->set_allowed_objects($object);
	    return $this;
	}
	
}