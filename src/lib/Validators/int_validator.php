<?php

namespace Sunhill\ORM\Validators;

class int_validator extends validator_base {
    
    protected function is_valid($test) {
        if (!ctype_digit($test) && !is_int($test)) {
            return false;
        }
        return true;    
    }
    
   
}