<?php

/**
 * @file TypeDate.php
 * Defines a type for datetime fields and a ancestor for date and time fields
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

class TypeDate extends TypeDateTime
{
       
    /**
     * The storage stores a datetime as a string in the form 'Y-m-d H:i:s'
     *
     * @param unknown $input
     * @return unknown, by dafult just return the value
     */
    protected function formatForStorage($input)
    {
        return $input->format('Y-m-d');
    }
    
    protected function formatForHuman($input)
    {
        return $input->format('j.n.Y');    
    }
    
    public function getAccessType(): string
    {
        return 'date';
    }
        
}