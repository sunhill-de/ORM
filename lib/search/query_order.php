<?php

namespace Sunhill\Search;

class query_order extends query_atom {
    
    protected $field;
    
    protected $direction;
    
    public function __construct(query_builder $parent_query,string $field,string $direction) {
        parent::__construct($parent_query);
        $this->field = $field;
        $this->direction = $direction;
    }
    
    public function get_query_part() {
        return ' order by '.$this->field." ".$this->direction;
    }
}