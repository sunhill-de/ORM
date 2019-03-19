<?php

namespace Sunhill\Properties;

class oo_property_enum extends oo_property {
	
	protected $type = 'enum';
	
	protected $features = ['object','simple'];
	
	private $allowed;
	
	protected function validate($value) {
	    if (!in_array($value, $this->allowed)) {
			throw new InvalidValueException("$value ist kein gültiger Enum-Wert.");
		}
		return $value;
	}
	
	public function set_enum_values($values) {
		if (is_array($values)) {
			$this->allowed = $values;
		} else {
			$this->allowed = array($values);
		}
		return $this;
	}
	
	public function set_values($values) {
		$this->set_enum_values($values);
		return $this;
	}
}