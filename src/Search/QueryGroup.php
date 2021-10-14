<?php

/**
 * @file QueryGroup.php
 * Provides the QueryGroup property
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

class QueryGroup extends QueryAtom 
{
    
    protected $alias;
    
    public function __construct(QueryBuilder $parent_query, string $alias) 
    {
        parent::__construct($parent_query);
        $this->alias = $alias;
    }
    
    public function getQueryPart() 
    {
        return ' group by '.$this->alias.".id";
    }
}
