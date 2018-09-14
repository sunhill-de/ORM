<?php

namespace Sunhill\Objects;

class oo_property_object_base extends oo_property {
	
	private $allowed_objects;
	
	public function set_allowed_objects($object) {
		if (!is_array($object)) {
			$this->allowed_objects = array($object);
		} else {
			$this->allowed_objects = $object;
		}
	}
	
	protected function is_allowed_object($test) {
		if (!isset($this->allowed_objects)) {
			return true;
		}
		foreach ($this->allowed_objects as $object) {
			if (is_a($test,$object)) {
				return true;
			}
		}
		return false;
	}

	protected function validate($value) {
		if (!$this->is_allowed_object($value)) {
			throw new InvalidValueException("Übergebenes Objekt ist kein erlaubtes Objekt.");
		} else {
			return $value;
		}
	}
	
}