<?php

/**
 * @file QueryWhereArrayOfObjects.php
 * Provides the QueryWhereArrayOfObject class
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

class QueryWhereArrayOfObjects extends QueryWhereArray 
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
        if (is_int($value)) {
            return ' = '.$this->escape($value);
        } else if (is_object($value)) {
            return ' = '.$this->escape($value->getID());
        } else if (is_array($value)) {
            $result = ' in (';
            $first = true;
            foreach ($value as $entry) {
                if (!$first) {
                    $result .= ',';
                }
                if (is_int($entry)) {
                    $result .= $this->escape($entry);                    
                } else if (is_object($entry)) {
                    $result .= $this->escape($entry->getID());
                }
                $first = false;
            }
            return $result.')';
        }
    }
    
    protected function getElement($element) 
    {
        if (is_int($element)) {
            return ' = '.$this->escape($element);
        } else if (is_object($element)) {
            return ' = '.$this->escape($element->getID());
        }
    }
    
    
    
}