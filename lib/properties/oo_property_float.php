<?php

namespace Sunhill\Properties;

class oo_property_float extends oo_property {
	
	protected $type = 'float';
	
	protected $features = ['object','simple'];
	
	protected function validate($value) {
		if (!is_numeric($value)) {
			throw new InvalidValueException("$value ist kein gültiger Float.");
		}
		return $value;
	}
}