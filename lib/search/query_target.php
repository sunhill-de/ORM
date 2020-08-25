<?php

namespace Sunhill\Search;

abstract class query_target extends query_atom {
    
    protected $table_id;
    
    public function __construct(query_builder $parent_query) {
        parent::__construct($parent_query);
        $this->table_id = $parent_query->get_table($parent_query->get_calling_class()::$table_name);
    }
    
}