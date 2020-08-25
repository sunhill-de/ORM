<?php

namespace Sunhill\Search;

abstract class query_atom {
    
    protected $parent_query;
    
    protected $next;
    
    protected $is_singleton = false;
    
    protected $order = 0;
    
    /**
     * Creates a new query_atom and passes the parent query over
     * @param query_builder $parent_query
     */
    public function __construct(query_builder $parent_query) {
        $this->parent_query = $parent_query;
    }
    
    /**
     * Links an atom to this atom or raises an exception if this is a singleton atom
     * @param $next the linked following atom
     * @param $connection the kind of connection between this two
     */
    public function link(query_atom $next,string $connection='and') {
        if ($this->is_singleton()) {
            throw new QueryException("A singleton query atom can't be linked.");
        }
        if (is_null($this->next)) {
            $this->next = $next;
            $this->connection = $connection;            
        } else {
            $this->next->link($next,$connection);
        }
    }
    
    /**
     * Returns if this atom is a singleton or linkable
     * @return unknown
     */
    public function is_singleton() {
        return $this->is_singleton;
    }
    
    /**
     * Return the order of this query part
     * @return number
     */
    protected function get_order() {
        return $this->order;
    }
    
    /**
     * Returns that part of query, that is needed for the complete query
     */
    abstract public function get_query_part();
}