<?php
/**
 *
 * @file ValidatorBase.php
 * Provides the base class for the validators
 * Lang en
 * Reviewstatus: 2020-08-10
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 */

namespace Sunhill\ORM\Validators;


/**
 * Baseclass for all kinds of validators
 * @author klaus
 *
 */
class ValidatorBase {
    
    /**
     * Checks if the given @param $test is a valid value
     * @param unknown $test
     * @return boolean, true, if it's valid otherwise false
     */
    protected function isValid($test) {
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
     * @return \Sunhill\ORM\Validators\unknown
     */
    public function validate($test) {
        if (!$this->isValid($test)) {
            if (is_object($test)) {
                $value = getClass($test);                
            } else if (is_array($test)) {
                $value = 'array';
            } else {
                $value = strval($test);
            } 
            throw new ValidatorException(getClass($this).": The given value '$value' is not valid.");
        }
        return $this->prepare($test);
    }

    /**
     * Checks only if the @param $test is valid without raising an exception
     * @param unknown $test
     * @return \Sunhill\ORM\Validators\boolean,
     */
    public function checkValidity($test) {
        return $this->isValid($test);
    }
    
}