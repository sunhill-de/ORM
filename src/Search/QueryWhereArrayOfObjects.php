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
        return 'objectobjectassigns';
    }
    
    protected function getAssocField()
    {
        return 'element_id';
    }
    
    protected function getElementIDList($value) 
    {
        if (is_int($value)) {
            return ' = '.$this->escape($value)." and field = '".$this->field."'";
        } else if (is_object($value)) {
            return ' = '.$this->escape($value->getID())." and field = '".$this->field."'";
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
            return $result.')'." and field = '".$this->field."'";
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