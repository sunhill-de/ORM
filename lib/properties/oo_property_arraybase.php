<?php

namespace Sunhill\Properties;

class oo_property_arraybase extends oo_property {

	protected $type = 'array_of_objects';
	
	protected $features = ['object','complex','array'];
	
	protected $initialized = true;
	
	protected function initialize() {
		$this->initialized = true;	
	}

}