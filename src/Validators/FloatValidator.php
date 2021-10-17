<?php

namespace Sunhill\ORM\Validators;

class FloatValidator extends ValidatorBase {
    
    protected function isValid($test) {
        if (!is_numeric($test)) {
            return false;
        }
        return true;
    }
    
   
}