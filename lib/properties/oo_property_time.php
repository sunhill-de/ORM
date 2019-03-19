<?php

namespace Sunhill\Properties;

class oo_property_time extends oo_property_datetime_base {

	protected $type = 'time';

	protected $features = ['object','simple'];
	
	protected function validate($value) {
		if (!($value = self::is_valid_time($value))) {
			throw new InvalidValueException("$value ist keine gültige Zeit.");
		}
		return $value;
	}
	
}