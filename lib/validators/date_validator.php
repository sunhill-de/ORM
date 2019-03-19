<?php

namespace Sunhill\Validators;

class date_validator extends datetime_validator {
    
    protected function is_valid($test) {
        if (!$this->is_valid_date($test,true)) {
            return false;
        }
        return true;    
    }
    
    protected function prepare($test) {
        return $this->is_valid_date($test,true);
    }
}