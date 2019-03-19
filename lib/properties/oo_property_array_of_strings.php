<?php

namespace Sunhill\Properties;

class oo_property_array_of_strings extends oo_property {
	
	protected $type = 'array_of_strings';
	
	protected $is_simple = false;
	
	protected $is_array = true;
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	
}