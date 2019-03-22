<?php

namespace Sunhill\Properties;

class oo_property_array_of_objects extends oo_property_arraybase {

	protected $type = 'array_of_objects';
		
	protected $model_name;
	
	protected $features = ['object','complex','array'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	protected function initialize() {
		$this->initialized = true;	
	}
	
	public function set_allowed_objects($object) {
	    $this->validator->set_allowed_objects($object);
	    return $this;
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
	
	public function load(int $id) {
	    $references = \App\objectobjectassign::where('container_id','=',$id)
	                                           ->where('field','=',$this->get_name())->get();
	    foreach ($references as $reference) {
	        $object = \Sunhill\Objects\oo_object::load_object_of($reference->element_id);
	        $this->value[$reference->index] = $object;
	    }
	    
	}
}