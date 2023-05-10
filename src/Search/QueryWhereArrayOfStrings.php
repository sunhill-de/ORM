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
        return $this->table_name.'_array_'.$this->field.' as '.$this->help_alias;
    }
    
    protected function getAssocField() 
    {
        return 'target';
    }
    
    protected function getElementIDList($value) 
    {
        if (is_string($value)) {
            return ' = '.$this->escape($value);
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
            return $result.')';
        }
    }

    protected function getElement($element) 
    {
        return ' = '.$this->escape($element);
    }
    
}