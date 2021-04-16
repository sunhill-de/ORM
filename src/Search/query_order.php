<?php

namespace Sunhill\ORM\Search;

class query_order extends query_atom {
    
    protected $field;
    
    protected $direction;
    
    public function __construct(query_builder $parent_query,string $field,$direction=true) {
        parent::__construct($parent_query);
        $this->field = $field;
        if (is_bool($direction)) {
            $this->direction = $direction?'asc':'desc';
        } else {
            if (($direction !== 'asc') && ($direction !== 'desc')) {
                throw new QueryException("Unknown direction '$direction'");
            }
            $this->direction = $direction;
        }
    }
    
    public function get_query_part() {
        return ' order by '.$this->field." ".$this->direction;
    }
}