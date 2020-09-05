<?php

namespace Sunhill\Search;

class query_limit extends query_atom {
    
    protected $delta;
    
    protected $limit;
    
    public function __construct(query_builder $parent_query,int $delta,int $limit) {
        parent::__construct($parent_query);
        $this->delta = $delta;
        $this->limit = $limit;
    }
    
    public function get_query_part() {
        return ' limit '.$this->delta.",".$this->limit;
    }
}