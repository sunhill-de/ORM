<?php

/**
 * @file TypeFloat.php
 * Defines a type for floats
 * Lang en
 * Reviewstatus: 2024-02-28
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

class TypeFloat extends TypeNumeric
{
   
    /**
     * Tells getHumanValue how many digits after the comma should be diaplayed
     * 
     * @var integer
     */
    protected $precision = 2;
    
    public function setPrecision(int $digits)
    {
       $this->precision = $digits; 
    }
    
    /**
     * Getter for precision 
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;    
    }
    
    protected function isNumericType($input): bool
    {
        return is_numeric($input);
    }

    public function getAccessType(): string
    {
        return 'float';
    }
    
    protected function formatForHuman($input)
    {
        return round($input, $this->precision);
    }
}