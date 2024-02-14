<?php
/**
 * @file AbstractSimpleProperty.php
 * Defines an abstract property as base for all properties that are a simple type (not array, not record)
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Exceptions\InvalidValueException;
use Illuminate\Support\Facades\Log;

abstract class AbstractSimpleProperty extends AbstractProperty
{
    
    /**
     * Tries to pass a verbouse error message to the log
     *
     * @param string $message
     */
    protected function error(string $message)
    {
        if (empty($this->owner)) {
            Log::error($message);
        } else {
            Log::error($this->owner->getName().': '.$message);
        }
    }
    
    /**
     * Returns true, if the given value is accepted as an input value for this validator
     *
     * @param unknown $input The value to test
     * @return bool true if valid otherwise false
     */
    abstract public function isValid($input): bool;
    
    /**
     * Checks if the given input value is acceptes, If not it raises an exception
     *
     * @param unknown $input
     * @throws InvalidValudException is thrown when the given valu is not valid
     */
    protected function validateInput($input)
    {
        if (!$this->isValid($input)) {
            if (is_scalar($input)) {
                $this->error("The value '$input* is not valid.");
                throw new InvalidValueException("The value '$input' is not valid.");
            } else {
                $this->error("The non scalar value is not valid.");
                throw new InvalidValueException("The value is not valuid.");
            }
        }
    }
    
    /**
     * Converts the input to an defined value to store. For example an object is returned as
     * a object instance even if only a id is passed. By default this method just passes the input data
     *
     * @param unknown $input
     */
    protected function doConvertToInput($input)
    {
        return $input;
    }
    
    /**
     * Checks if the given input is valid. If yes try to convert it to a internal value
     *
     * @param unknown $input
     * @return unknown
     */
    public function convertToInput($input)
    {
        $this->validateInput($input);
        return $this->doConvertToInput($input);
    }
    
    /**
     * When a value is requested it it passed through this method to eventually perform a
     * processing first
     
     * @param unknown $output
     * @return unknown By default just the output
     */
    protected function convertToOutput($output)
    {
        return $output;
    }
    
    /**
     * Sometimes the value is stored in the storage in another format than it is in the property
     *
     * @param unknown $input
     * @return unknown, by dafult just return the value
     */
    public function doConvertToStorage($input)
    {
        return $input;
    }
    
    /**
     * Sometimen the value was stored in the storage in another format than it is in the property
     *
     * @param unknown $output
     * @return unknown,. by default just pass the value
     */
    public function doConvertFromStorage($output)
    {
        return $output;
    }
    
    
}