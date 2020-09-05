<?php
/**
 *
 * @file validator_base.php
 * Provides the base class for the validators
 * Lang en
 * Reviewstatus: 2020-08-10
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 */

namespace Sunhill\Validators;

/**
 * Exception that is raised by validators
 * @author klaus
 *
 */
class ValidatorException extends \Exception {};

/**
 * Baseclass for all kinds of validators
 * @author klaus
 *
 */
class validator_base {
    
    /**
     * Checks if the given @param $test is a valid value
     * @param unknown $test
     * @return boolean, true, if it's valid otherwise false
     */
    protected function is_valid($test) {
        return true;    
    }
    
    /**
     * Normalizes the given @param $test for further procession
     * @param unknown $test
     * @return unknown
     */
    protected function prepare(&$test) {
        return $test;
    }
    
    /**
     * Performs the validation and raises an exception if its not valid
     * @param unknown $test
     * @throws ValidatorException
     * @return \Sunhill\Validators\unknown
     */
    public function validate($test) {
        if (!$this->is_valid($test)) {
            if (is_object($test)) {
                $value = get_class($test);                
            } else if (is_array($test)) {
                $value = 'array';
            } else {
                $value = strval($test);
            } 
            throw new ValidatorException(get_class($this).": The given value '$value' is not valid.");
        }
        return $this->prepare($test);
    }

    /**
     * Checks only if the @param $test is valid without raising an exception
     * @param unknown $test
     * @return \Sunhill\Validators\boolean,
     */
    public function check_validity($test) {
        return $this->is_valid($test);
    }
    
}