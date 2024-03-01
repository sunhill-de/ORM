<?php

/**
 * @file TypeBoolean.php
 * Defines a type for booleans
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

use Sunhill\ORM\Properties\AbstractSimpleProperty;

class TypeBoolean extends AbstractSimpleProperty
{
    
    /**
     * Tests if input can be solved to an boolean (always true, because everything can be solved to
     * a boolean
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return true;
    }
    
    protected function convertString(string $input)
    {
        return in_array(strtolower($input),[1,'1','y','true','+'])?1:0;        
    }
    
    /**
     * Translates the given input to 1 or 0
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::doConvertToInput()
     */
    protected function formatForStorage($input)
    {
        if (is_string($input)) {
            return $this->convertString($input);
        }
        return empty($input)?0:1;
    }

    protected function formatForHuman($input)
    {
        if ($input) {
            return __('true');
        } else {
            return __('false');
        }
    }
    
    public function getAccessType(): string
    {
        return 'boolean';
    }
    
}
