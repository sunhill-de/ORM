<?php

namespace Sunhill\Properties;

class oo_property_object extends oo_property_field {
	
	protected $type = 'object';
	
	protected $features = ['object','complex'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	public function set_allowed_objects($object) {
	    $this->validator->set_allowed_objects($object);
	    return $this;
	}
	
	public function load(int $id) {
	    $reference = \App\objectobjectassign::where('container_id','=',$id)
	               ->where('field','=',$this->get_name())->first();
	    if (!empty($reference)) {
    	    $object = \Sunhill\Objects\oo_object::load_object_of($reference->element_id);
    	    $this->value = $object;
	    }
	}
	
}