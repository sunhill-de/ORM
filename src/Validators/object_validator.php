<?php

namespace Sunhill\ORM\Validators;

use Sunhill\ORM\Traits\TestObject;

class object_validator extends validator_base {
    
    use TestObject;
    
    private $allowed_objects;
    
    public function set_allowed_objects($object) {
        if (!is_array($object)) {
            $this->allowed_objects = array($object);
        } else {
            $this->allowed_objects = $object;
        }
        return $this;
    }
    
    protected function is_allowed_object($test) {
        if (!isset($this->allowed_objects)) {
            return true;
        }
        return $this->is_valid_object($test,$this->allowed_objects);
    }
    
    protected function is_valid($value) {
        if (!$this->is_allowed_object($value)) {
            return false;
        } else {
            return true;
        }
    }
       
}