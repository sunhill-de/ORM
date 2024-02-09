<?php

/**
 * @file TypeDateTime.php
 * Defines a type for datetime fields and a ancestor for date and time fields
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

use Sunhill\ORM\Properties\Exceptions\InvalidParameterException;

class TypeDateTime extends AbstractType
{
   
    
    /**
     * First check if the given value is an ingteger at all all. afterwards check the boundaries
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        if (is_a($input, \DateTime::class)) {
            return true;
        }
        if (is_numeric($input)) {
            $input = '@'.$input;
        }
        try {
            $date = new \DateTime($input);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    
    /**
     * Cuts the input string to a maximum length
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::doConvertToInput()
     */
    protected function doConvertToInput($input)
    {
        if (is_a($input, \DateTime::class)) {
            return $input;
        }
        return new \DateTime($input);        
    }
}