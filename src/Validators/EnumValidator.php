<?php

namespace Sunhill\ORM\Validators;

class EnumValidator extends ValidatorBase {
    
    private $allowed;
    
    protected function isValid($test) {
        if (!in_array($test, $this->allowed)) {
            return false;
        }
        return true;
    }
    
    public function setEnumValues($values) {
        if (is_array($values)) {
            $this->allowed = $values;
        } else {
            $this->allowed = array($values);
        }
        return $this;
    }
    
    public function getEnumValues() {
        return $this->allowed;
    }
}