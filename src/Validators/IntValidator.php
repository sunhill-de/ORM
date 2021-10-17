<?php

namespace Sunhill\ORM\Validators;

class IntValidator extends ValidatorBase {
    
    protected function isValid($test) {
        if (!ctype_digit($test) && !is_int($test)) {
            return false;
        }
        return true;    
    }
    
   
}