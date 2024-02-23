<?php

/**
 * @file AbstractType.php
 * Defines a basic validator for types
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

use Sunhill\ORM\Properties\AbstractSimpleProperty;

class TypeText extends AbstractSimpleProperty
{
   
    /**
     * Is only has to be scalar 
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return is_scalar($input);
    }

    public function getAccessType(): string
    {
        return 'string';
    }
        
}