<?php

namespace Sunhill\Validators;

class enum_validator extends validator_base {
    
    private $allowed;
    
    protected function is_valid($test) {
        if (!in_array($test, $this->allowed)) {
            return false;
        }
        return true;
    }
    
    public function set_enum_values($values) {
        if (is_array($values)) {
            $this->allowed = $values;
        } else {
            $this->allowed = array($values);
        }
        return $this;
    }
    
    public function get_enum_values() {
        return $this->allowed;
    }
}