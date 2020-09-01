<?php

namespace Sunhill\Search;

class query_where_object extends query_where {
    
    protected $allowed_relations = 
        [
            '='=>'scalar',
            '!='=>'scalar',
            '<>'=>'scalar',
            'in'=>'array',            
        ];
    
        public function get_query_part() {
            $result = $this->get_query_prefix();
            switch ($this->relation) {
                case '=':
                    if (is_null($this->value)) {
                        $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."')";
                    } else {
                        $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id = '".$this->value."')";
                    }
                    break;
                case '<>':
                case '!=':
                    if (is_null($this->value)) {
                        $result .= " a.id in (select container_id from objectobjectassigns where field = '".$this->field."')";
                    } else {
                        $result .= " a.id not in (select container_id from objectobjectassigns where field = '".$this->field."' and element_id = '".$this->value."')";                        
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
            return $result;
        }
}