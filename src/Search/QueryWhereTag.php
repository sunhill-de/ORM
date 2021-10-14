<?php

/**
 * @file QueryWhereTag.php
 * Provides the QueryWhereTag class
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

use Sunhill\ORM\Properties\Property;

class QueryWhereTag extends QueryWhereArray 
{
    
    protected function getAssocTable() 
    {
        return 'tagobjectassigns';
    }
    
    protected function getAssocField() 
    {
        return 'tag_id';
    }
    
    protected function getElementIDList($value) 
    {
        if (is_int($value)) {
            return ' = '.$this->escape($value);
        } else if (is_string($value)) {
            return ' in (select tag_id from tagcache where name = '.$this->escape($value).')';
        } else if (is_array($value)) {
            $itemlist = '';
            $first = true;
            foreach ($value as $items) {
                if (!$first) {
                    $itemlist .= ',';
                }
                $first = false;
                $itemlist .= $this->escape($items);
            }
            
            if (is_int($value[0])) {
                return ' in ('.$itemlist.')';
            } else if (is_string($value[0])) {
                return ' in (select tag_id from tagcache where name in ('.$itemlist.'))';
            }
        }
    }
       
    protected function getElement($element) 
    {
        if (is_int($element)) {
            return ' = '.$this->escape($element);
        } else if (is_string($element)) {
            return ' in (select tag_id from tagcache where name = '.$this->escape($element).')';
        }
    }
    
}