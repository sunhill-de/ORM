<?php

namespace Sunhill\Search;

class query_where_array_of_objects extends query_where_array {

    protected function get_assoc_table() {
        return 'objectobjectassigns';
    }
    
    protected function get_assoc_field() {
        return 'element_id';
    }
    
    protected function get_element_id_list($value) {
        if (is_int($value)) {
            return ' = '.$this->escape($value);
        } else if (is_object($value)) {
            return ' = '.$this->escape($value->get_id());
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
            return $result.')';
        }
    }
    
}