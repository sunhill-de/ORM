<?php

namespace Sunhill\Properties;

class oo_property_array_of_strings extends oo_property {
	
	protected $type = 'array_of_strings';
	
	protected $features = ['object','complex','array'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	
}