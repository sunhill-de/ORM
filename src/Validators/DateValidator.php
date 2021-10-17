<?php

namespace Sunhill\ORM\Validators;

class DateValidator extends DatetimeValidator {
    
    protected function isValid($test) {
        if (!$this->isValidDate($test,true)) {
            return false;
        }
        return true;    
    }
    
    protected function prepare(&$test) {
        return $this->isValidDate($test,true);
    }
}