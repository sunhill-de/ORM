<?php

namespace Sunhill\Properties;

class PropertyException extends \Exception {}

class InvalidValueException extends PropertyException {}

class oo_property extends \Sunhill\base {
	
    protected $features = array();
    
	protected $owner;
	
	protected $name;
	
	protected $value;
	
	protected $shadow;
	
	protected $type;
	
	protected $default;
	
	protected $defaults_null;
	
	protected $dirty;
	
	protected $initialized;
	
	protected $model_name;
	
	protected $read_only;
	
	protected $validator_name = 'validator_base';
	
	protected $validator;
	
	protected $hooks = array();
	
	protected $class;
	
	public function __construct() {
		$this->dirty = false;
		$this->initialized = false;
		$this->defaults_null = false;
		$this->read_only = false;
		if ($this->is_array()) {
			$this->value = array();
		}
		$this->initialize();
		$this->init_validator();
	}
	
	protected function initialize() {
		
	}
	
	protected function init_validator() {
	    $validator_name = "\\Sunhill\\Validators\\".$this->validator_name;
	    $this->validator = new $validator_name();    
	}
	
	public function set_owner($owner) {
	    $this->owner = $owner;
	    return $this;	    
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
		    $oldvalue = $this->value;
		    if (!$this->dirty) {
		        $this->shadow = $this->value;
		        $this->dirty = true;
		    }
		    $this->value = (is_null($value)?null:$this->validate($value));
			$this->initialized = true;
			$this->value_changed($oldvalue,$this->value);
		}
		return $this;
	}
	
	protected function value_changed($from,$to) {
	    
	}
	
	public function &get_value() {
		if (!$this->initialized) {
			if (isset($this->default) || $this->defaults_null) {
				$this->value = $this->default;
				$this->shadow = $this->default;
				$this->initialized = true;
			} else {
			    throw new PropertyException("Lesender Zugriff auf nicht ininitialisierte Property: '".$this->name."'");
			}
		}
		if ($this->is_array()) {
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
	    return $this;
	}
	
	public function get_model() {
		return $this->model_name;
	}
	
	public function set_class(string $class) {
	   $this->class = $class;
	   return $this;
	}
	
	public function get_class() {
	    return $this->class;
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
		return $this->validator->validate($value);
	}
	
	public function set_readonly($value) {
		$this->read_only = $value;
		return $this;
	}
	
	public function get_readonly() {
		return $this->read_only;
	}	
	
	public function is_array() {
		return $this->has_feature('array');
	}
	
	public function is_simple() {
		return $this->has_feature('simple');
	}
	
	public function has_feature(string $test) {
	    return in_array($test,$this->features);
	}
	
	/**
	 * Wird aufgerufen, bevor das Elternobjekt ein update erhält
	 */
	public function updating(int $id) {
	    
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt ein update erhalten hat
	 */
	public function updated(int $id) {
	    $this->commit();
	}
	
	/**
	 * Wird aufgerufen, bevor das Elternobjekt eingefügt wurde
	 */
	public function inserting() {
	    
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 */
	public function inserted(int $id) {
	    $this->commit();	    
	}
	
	public function deleting(int $id) {
	    
	}
	
	public function deleted(int $id) {
	    
	}
	
	public function get_diff_array() {
	    return array('FROM'=>$this->get_old_value(),
	                 'TO'=>$this->get_value());
	}
	
	public function load(int $id) {
	    $this->initialized = true; 
	}
	
	public function add_hook($action,$hook,$subaction,$target) {
	   $this->hooks[] = ['action'=>$action,'hook'=>$hook,'subaction'=>$subaction,'target'=>$target];    
	}
}