<?php

namespace Sunhill\Search;

abstract class query_where_tag extends query_where {
    
    protected $field;
    
    protected $relation;
    
    protected $value;
    
    protected $allowed_relations = ['has','has not','one of','all of','none of'];
    
    public function __construct(query_builder $parent_query,string $field,string $relation,string $value) {
        parent::__construct($parent_query);
        if (!$this->is_allowed_relation($relation)) {
            throw new QueryException("'$relation' is not an allowed relation in this context.");
        }
    }
    
    /**
     * Checks, if this relation is allowed in this context
     * @param string $relation
     * @return boolean true, if its an allowed relation, otherwise false
     */
    protected function is_allowed_relation(string $relation) {
        return in_array($relation,$this->allowed_relations);
    }
}