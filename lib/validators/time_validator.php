<?php

namespace Sunhill\Validators;

class time_validator extends datetime_validator {
    
    protected function is_valid($test) {
            if (!($test = self::is_valid_time($test))) {
                return false;
            }
            return true;
    }
       
    protected function prepare($test) {
        return $this->is_valid_time($test);
    }
    
}