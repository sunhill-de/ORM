<?php

namespace Sunhill\ORM\Search;

class query_where_array_of_objects extends query_where_array {

    protected function get_assoc_table() {
        return 'objectobjectassigns';
    }
    
    protected function get_assoc_field() {
        return 'element_id';
    }
    
    protected function get_element_id_list($value) {
        if (is_int($value)) {
            return ' = '.$this->escape($value)." and field = '".$this->field."'";
        } else if (is_object($value)) {
            return ' = '.$this->escape($value->get_id())." and field = '".$this->field."'";
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
                    $result .= $this->escape($entry->get_id());
                }
                $first = false;
            }
            return $result.')'." and field = '".$this->field."'";
        }
    }
    
    protected function get_element($element) {
        if (is_int($element)) {
            return ' = '.$this->escape($element);
        } else if (is_object($element)) {
            return ' = '.$this->escape($element->get_id());
        }
    }
    
    
    
}