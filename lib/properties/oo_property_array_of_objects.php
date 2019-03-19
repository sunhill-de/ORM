<?php

namespace Sunhill\Properties;

class oo_property_array_of_objects extends oo_property_object_base {

	protected $type = 'array_of_objects';
	
	protected $features = ['object','complex','array'];
	
	protected $initialized = true;
	
	protected function initialize() {
		$this->initialized = true;	
	}

}