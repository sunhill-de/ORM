<?php

/**
 * @file TypeEnum.php
 * Defines a type for enums
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

use Sunhill\ORM\Properties\AbstractSimpleProperty;

class TypeEnum extends AbstractSimpleProperty
{
   
    /**
     * Indicates how long the string may be
     * 
     * @var integer
     */
    protected $allowed_values = [];
    
    /**
     * Setter for allowed_values
     * 
     * @param int $enum_values
     * @return \Sunhill\ORM\Properties\Types\TypeVarchar
     */
    public function setEnumValues(array $enum_values)
    {
        $this->allowed_values = $enum_values;
        return $this;
    }
    
    /**
     * Getter for allowed_values
     * 
     * @return int
     */
    public function getEnumValues(): array
    {
        return $this->allowed_values;    
    }
    
    /**
     * Checks if the given input value is in allowed_values
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return in_array($input, $this->allowed_values);
    }
    
    public function getAccessType(): string
    {
        return 'string';
    }
        
}