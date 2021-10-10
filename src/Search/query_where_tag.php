<?php

namespace Sunhill\ORM\Search;

use Sunhill\ORM\Properties\Property;

class query_where_tag extends query_where_array {
    
    protected function get_assoc_table() {
        return 'tagobjectassigns';
    }
    
    protected function get_assoc_field() {
        return 'tag_id';
    }
    
    protected function get_element_id_list($value) {
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
       
    protected function get_element($element) {
        if (is_int($element)) {
            return ' = '.$this->escape($element);
        } else if (is_string($element)) {
            return ' in (select tag_id from tagcache where name = '.$this->escape($element).')';
        }
    }
    
}