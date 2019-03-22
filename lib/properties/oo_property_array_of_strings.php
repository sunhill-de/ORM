<?php

namespace Sunhill\Properties;

class oo_property_array_of_strings extends oo_property_arraybase {
	
	protected $type = 'array_of_strings';
	
	protected $model_name;
	
	protected $features = ['object','complex','array'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	public function set_type($type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
	
	public function set_model($name) {
	    if (strpos($name,'\\') === false) {
	        $this->model_name = $this->owner->default_ns.'\\'.$name;
	    } else {
	        $this->model_name = $name;
	    }
	    return $this;
	}
	
	public function get_model() {
	    return $this->model_name;
	}
	
}