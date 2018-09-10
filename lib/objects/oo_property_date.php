<?php

namespace Sunhill\Objects;

class oo_property_date extends oo_property_datetime_base {
	
	protected $type = 'date';
	
	protected function validate($value) {
		if (!($value = self::is_valid_date($value))) {
			throw new InvalidValueException("$value ist kein gültiges Datum.");
		}
		return $value;
	}
	
}