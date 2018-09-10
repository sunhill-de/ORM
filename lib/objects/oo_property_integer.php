<?php

namespace Sunhill\Objects;

class oo_property_integer extends oo_property {
	
	protected $type = 'integer';
	
	protected function validate($value) {
		if (!ctype_digit($value) && !is_int($value)) {
			throw new InvalidValueException("$value ist kein gültiger Integer.");
		}
		return $value;
	}
}