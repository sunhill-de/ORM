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
    protected function formatForStorage($input)
    {
        return $input->format('H:i:s');
    }
    
    /**
     * Formats the time in a human readable format
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\Types\TypeDateTime::formatForHuman()
     */
    protected function formatForHuman($input)
    {
        return $input->format('H:i:s');
    }
    
    /**
     * Returns the access type (in this case 'time')
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\Types\TypeDateTime::getAccessType()
     */
    public function getAccessType(): string
    {
        return 'time';
    }
        
}