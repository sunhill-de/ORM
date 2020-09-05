<?php

namespace Sunhill\Search;

class query_group extends query_atom {
    
    protected $alias;
    
    public function __construct(query_builder $parent_query,string $alias) {
        parent::__construct($parent_query);
        $this->alias = $alias;
    }
    
    public function get_query_part() {
        return ' group by '.$this->alias.".id";
    }
}