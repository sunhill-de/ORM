<?php

namespace Sunhill\Search;

abstract class query_where_array extends query_where {
    
    protected $allowed_relations = 
        [
                'has'=>'scalar',
                'has not'=>'scalar',
                'one of'=>'array',
                'all of'=>'array',
                'none of'=>'array',
                'has any'=>'unary',
                'not empty'=>'unary',
                'has none'=>'unary',
                'empty'=>'unary'
        ];
    
        abstract protected function get_assoc_table();
        abstract protected function get_assoc_field();
        abstract protected function get_element_id_list($value);
        abstract protected function get_element($element);
        
        public function get_this_where_part() {
            $result = $this->get_query_prefix();
            switch ($this->relation) {
                case 'has':
                case 'one of':
                    $result .= ' a.id in (select container_id from '.$this->get_assoc_table().' where '.$this->get_assoc_field().' '.$this->get_element_id_list($this->value).')';
                    break;
                case 'has not':
                case 'none of':
                    $result .= ' a.id not in (select container_id from '.$this->get_assoc_table().' where '.$this->get_assoc_field().' '.$this->get_element_id_list($this->value).')';
                    break;
                case 'has any':
                case 'not empty':
                    $result .= ' a.id in (select container_id from tagobjectassigns)';
                    break;
                case 'has none':
                case 'empty':
                    $result .= ' a.id not in (select container_id from tagobjectassigns)';
                    break;
                case 'all of':
                    $result .= ' ('.$this->get_all_of_part().')';
                    break;
            }
            return $result;
        }
        
        protected function get_all_of_part() {
            $result = '';
            $first = true;
            foreach ($this->value as $value) {
                $value = $this->get_element($value);
                $result .= ($first?'':' and ').' exists (select container_id from '.$this->get_assoc_table().' where container_id = a.id and '.$this->get_assoc_field().$value.')';
                $first = false;
            }
            return $result;
        }
}