<?php

/**
 * @file QueryLimit.php
 * Provides the QueryLimit class
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: none
 * Documentation: incomplete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Search;

class QueryLimit extends QueryAtom 
{
    
    protected $delta;
    
    protected $limit;
    
    public function __construct(QueryBuilder $parent_query, int $delta, int $limit) 
    {
        parent::__construct($parent_query);
        $this->delta = $delta;
        $this->limit = $limit;
    }
    
    public function getQueryPart() 
    {
        return ' limit '.$this->delta.",".$this->limit;
    }
}