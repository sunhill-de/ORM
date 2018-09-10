<?php

namespace Sunhill\Objects;

class PropertyException extends \Exception {}

class InvalidValueException extends PropertyException {}

class oo_property extends \Sunhill\base implements \ArrayAccess {
	
	protected $owner;
	
	protected $name;
	
	protected $value;
	
	protected $shadow;
	
	protected $type;
	
	protected $is_simple = true;
	
	protected $is_array = false;
	
	protected $default;
	
	protected $defaults_null;
	
	protected $dirty;
	
	protected $initialized;
	
	protected $model_name;
	
	protected $read_only;
	
	public function __construct($owner) {
		$this->owner = $owner;
		$this->dirty = false;
		$this->initialized = false;
		$this->defaults_null = false;
		$this->read_only = false;
		if ($this->is_array) {
			$this->value = array();
		}
		$this->initialize();
	}
	
	protected function initialize() {
		
	}
	
	public function set_name($name) {
		$this->name = $name;
		return $this;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function load_value($value) {
		$this->value = $value;
		$this->initialized = true;
		return $this;		
	}
	
	public function set_value($value) {
		if ($this->read_only) {
			throw new PropertyException("Die Property ist read-only.");
		}
		if ($value !== $this->value || !$this->initialized) {
			$this->value = (is_null($value)?null:$this->validate($value));
			$this->dirty = true;
			$this->initialized = true;
		}
		return $this;
	}
	
	public function get_value() {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;
				$this->shadow = $this->default;
				$this->initialized = true;
			} else {
				throw new PropertyException("Lesender Zugriff auf nicht ininitialisierte Property: '".$this->name."'");
			}
		}
		if ($this->is_array) {
			return $this;
		} else {
		    return $this->value;
		}
	}
	
	public function get_old_value() {
		return $this->shadow;
	}
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function get_dirty() {
		return $this->dirty;	
	}
	
	public function set_dirty($value) {
		$this->dirty = $value;
	}
	
	public function set_default($default) {
		if (!isset($default)) {
			$this->defaults_null = true;
		}
		$this->default = $default;
		return $this;
	}
	
	public function get_default() {
		return $this->default;
	}
	
	public function set_model($name) {
		$this->model_name = $name;
		return $this;
	}
	
	public function get_model() {
		return $this->model_name;
	}
	
	public function commit() {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;	
			} else {
				throw new PropertyException("Commit einer nicht initialisierten Property: '".$this->name."'");
			}
		}
		$this->dirty = false;
		$this->shadow = $this->value;
	}
	
	public function rollback() {
		$this->dirty = false;
		$this->value = $this->shadow;
	}
	
	protected function validate($value) {
		return $value;
	}
	
	public function set_readonly($value) {
		$this->read_only = $value;
		return $this;
	}
	
	public function get_readonly() {
		return $this->read_only;
	}	
	
	public function is_array() {
		return $this->is_array;
	}
	
	public function is_simple() {
		return $this->is_simple;
	}
	
	public function offsetExists($offset) {
		return isset($this->value[$offset]);
	}

	public function offsetGet($offset) {
		return $this->value[$offset];
	}
	
	public function offsetSet($offset, $value) {
		if (isset($offset)) {
			$this->value[$offset] = $this->validate($value);
		} else {
			$this->value[] = $this->validate($value);
		}
	}
	
	public function offsetUnset($offset) {
		unsset($this->value[$offset]);
	}
}