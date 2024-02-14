<?php

/**
 * @file TypeTime.php
 * Defines a type for time fields 
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

use Sunhill\ORM\Properties\Exceptions\InvalidParameterException;

class TypeTime extends TypeDateTime
{
       
    /**
     * The storage stores a datetime as a string in the form 'Y-m-d H:i:s'
     *
     * @param unknown $input
     * @return unknown, by dafult just return the value
     */
    public function doConvertToStorage($input)
    {
        return $input->format('H:i:m');
    }
     
    public function getAccessType(): string
    {
        return 'time';
    }
    
    
}