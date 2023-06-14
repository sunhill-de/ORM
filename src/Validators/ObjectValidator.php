<?php

namespace Sunhill\ORM\Validators;

use Sunhill\ORM\Traits\TestObject;

class ObjectValidator extends ValidatorBase {
    
    use TestObject;
    
    private $allowed_objects;
    
    public function setAllowedClasses($object) 
    {
        if (!is_array($object)) {
            $this->allowed_objects = array($object);
        } else {
            $this->allowed_objects = $object;
        }
        return $this;
    }
    
    public function getAllowedClasses()
    {
        return $this->allowed_objects;
    }
    
    protected function isAllowedObject($test) 
    {
        if (!isset($this->allowed_objects)) {
            return true;
        }
        return $this->isValidObject($test,$this->allowed_objects);
    }
    
    protected function isValid($value) 
    {
        if (!$this->isAllowedObject($value)) {
            return false;
        } else {
            return true;
        }
    }
       
}