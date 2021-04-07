<?php

namespace Sunhill\ORM\Search;

class query_where_object extends query_where {
    
    protected $allowed_relations = 
        [
            '='=>'object',
            '!='=>'object',
            '<>'=>'object',
            'in'=>'array',            
        ];
    
        protected function get_value() {
            if (is_int($this->value)) {
                return $this->escape($this->value);
            } else {
                return $this->escape($this->value->get_id());
            }
        }
        
        public function get_this_where_part() {
            $result = $this->get_query_prefix();
            switch ($this->relation) {
                case '=':
                    if (is_null($this->value)) {
                        $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."')";
                    } else {
                        $value = $this->get_value();
                        $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id = $value)";
                    }
                    break;
                case '<>':
                case '!=':
                    if (is_null($this->value)) {
                        $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."')";
                    } else {
                        $value = $this->get_value();
                        $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id = $value)";                        
                    }
                    break;
                case 'in':
                    $element_list = $this->get_element_list();
                    $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id in ($element_list)";
                    break;
                case 'not in':
                    $element_list = $this->get_element_list();
                    $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id in ($element_list)";
                    break;
            }
            return $result;
        }
        
        protected function get_element_list() {
            $result = '';
            $first = true;
            foreach ($this->value as $element) {
                if (!$first) {
                    $result .= ',';
                }
                if (is_int($element)) {
                    $result .= $this->escape($element);
                } else if (is_object($element)) {
                    $result .= $this->escape($element->get_id());
                }
                $first = false;
            }
            return $result.')';
        }
}