<?php

/**
 * @file TypeFloat.php
 * Defines a type for floats
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

class TypeFloat extends TypeNumeric
{
   
    protected function isNumericType($input): bool
    {
        return is_numeric($input);
    }

    public function getAccessType(): string
    {
        return 'float';
    }
    
}