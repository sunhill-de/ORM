<?php

/**
 * @file QueryAtom.php
 * Provides the varchar property
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests: 
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: incompleted
 */

namespace Sunhill\ORM\Search;

abstract class QueryAtom 
{
    
    protected $parent_query;
    
    protected $next;
    
    protected $prev;
    
    protected $is_singleton = false;
    
    protected $order = 0;
    
    protected $connection;
    
    /**
     * Creates a new QueryAtom and passes the parent query over
     * @param QueryBuilder $parent_query
     */
    public function __construct(QueryBuilder $parent_query) 
    {
        $this->parent_query = $parent_query;
    }
    
    public function setPrev(QueryAtom $prev) 
    {
        $this->prev = $prev;
    }
    
    /**
     * Links an atom to this atom or raises an exception if this is a singleton atom
     * @param $next the linked following atom
     * @param $connection the kind of connection between this two
     */
    public function link(QueryAtom $next, string $connection = 'and') 
    {
        if ($this->isSingleton()) {
            throw new QueryException("A singleton query atom can't be linked.");
        }
        if (is_null($this->next)) {
            $this->next = $next;
            $next->setPrev($this);
            $this->connection = $connection;            
        } else {
            $this->next->link($next,$connection);
        }
    }
    
    /**
     * Returns if this atom is a singleton or linkable
     * @return unknown
     */
    public function isSingleton() 
    {
        return $this->is_singleton;
    }
    
    /**
     * Return the order of this query part
     * @return number
     */
    protected function getOrder() 
    {
        return $this->order;
    }
    
    /**
     * Returns that part of query, that is needed for the complete query
     */
    abstract public function getQueryPart();
}