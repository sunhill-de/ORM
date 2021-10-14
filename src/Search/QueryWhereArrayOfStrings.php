<?php

/**
 * @file QueryWhereArrayOfStrings.php
 * Provides the QueryWhereArrayOfStrings class
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Search;

class QueryWhereArrayOfStrings extends QueryWhereArray 
{

    protected function getAssocTable() 
    {
        return 'stringobjectassigns';
    }
    
    protected function getAssocField() 
    {
        return 'element_id';
    }
    
    protected function getElementIDList($value) 
    {
        if (is_string($value)) {
            return ' = '.$this->escape($value)." and field = '".$this->field."'";
        } else if (is_array($value)) {
            $result = ' in (';
            $first = true;
            foreach ($value as $entry) {
                if (!$first) {
                    $result .= ',';
                }
                $result .= $this->escape($entry);                    
                $first = false;
            }
            return $result.')'." and field = '".$this->field."'";
        }
    }

    protected function getElement($element) 
    {
        return ' = '.$this->escape($element)." and field = '".$this->field."'";
    }
    
}