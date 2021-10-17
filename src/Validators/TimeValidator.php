<?php

namespace Sunhill\ORM\Validators;

class TimeValidator extends DatetimeValidator {
    
    protected function isValid($test) {
            if (!($test = self::isValidTime($test))) {
                return false;
            }
            return true;
    }
       
    protected function prepare(&$test) {
        return $this->isValidTime($test);
    }
    
}