<?php

namespace Sunhill\Search;

class query_where_array_of_strings extends query_where_array {

    protected function get_assoc_table() {
        return 'stringobjectassigns';
    }
    
    protected function get_assoc_field() {
        return 'element_id';
    }
    
    protected function get_element_id_list($value) {
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

    protected function get_element($element) {
        return ' = '.$this->escape($element)." and field = '".$this->field."'";
    }
    
}