<?php

namespace Sunhill\Objects;

class oo_property_float extends oo_property {
	
	protected $type = 'float';
	
	protected function validate($value) {
		if (!is_numeric($value)) {
			throw new InvalidValueException("$value ist kein gültiger Float.");
		}
		return $value;
	}
}