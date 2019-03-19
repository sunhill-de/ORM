<?php

namespace Sunhill\Properties;

class oo_property_object extends oo_property {
	
	protected $type = 'object';
	
	protected $features = ['object','complex'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	public function set_allowed_objects($object) {
	    $this->validator->set_allowed_objects($object);
	    return $this;
	}
}