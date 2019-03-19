<?php

namespace Sunhill\Validators;

class ValidatorException extends \Exception {};

class validator_base {
    
    protected function is_valid($test) {
        return true;    
    }
    
    protected function prepare($test) {
        return $test;
    }
    
    public function validate($test) {
        if (!$this->is_valid($test)) {
            throw new ValidatorException("Der übergebene Wert ist nicht valide.");
        }
        return $this->prepare($test);
    }
    
    public function check_validity($test) {
        return $this->is_valid($test);
    }
    
}