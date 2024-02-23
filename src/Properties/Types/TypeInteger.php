<?php

/**
 * @file AbstractInteger.php
 * Defines a type to integers
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

class TypeInteger extends TypeNumeric
{
   
    protected function isNumericType($input): bool
    {
        return (ctype_digit($input) || is_int($input));
    }
    
    public function getAccessType(): string
    {
        return 'integer';
    }
    
}