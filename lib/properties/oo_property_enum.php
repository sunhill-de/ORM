<?php

namespace Sunhill\Properties;

class oo_property_enum extends oo_property {
	
	protected $type = 'enum';
	
	protected $features = ['object','simple'];
	
	protected $validator_name = 'enum_validator';
	
	
	public function set_enum_values($values) {
        $this->validator->set_enum_values($values);
	    return $this;
	}
	
	public function set_values($values) {
		$this->set_enum_values($values);
		return $this;
	}
}