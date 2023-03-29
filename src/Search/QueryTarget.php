<?php

/**
 * @file QueryTargert.php
 * Provides the QueryTarger class
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

abstract class QueryTarget extends QueryAtom 
{
    
    protected $table_id;
    
    public function __construct(QueryBuilder $parent_query) 
    {
        parent::__construct($parent_query);
        $this->table_id = $parent_query->getTable($parent_query->get_calling_class()::getInfo('table'));
    }
    
}