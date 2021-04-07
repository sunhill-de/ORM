<?php

namespace Sunhill\ORM\Validators;

class float_validator extends validator_base {
    
    protected function is_valid($test) {
        if (!is_numeric($test)) {
            return false;
        }
        return true;
    }
    
   
}