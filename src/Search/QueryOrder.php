<?php

/**
 * @file QueryOrder.php
 * Provides the QueryOrder class
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

use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;

class QueryOrder extends QueryAtom 
{
    
    protected $field;
    
    protected $direction;
    
    public function __construct(QueryBuilder $parent_query, Property $field, $direction = true) 
    {
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
        $this->getTable($field);
    }
    
    protected function getTable(Property $field)
    {
        $this->alias = $this->parent_query->getTable($this->getTableName($field));
        $this->field = $field->getName();
    }
    
    protected function getTableName(Property $field)
    {
        return Classes::getTableOfClass($field->getClass());
    }
    
    public function getQueryPart() 
    {
        return ' order by '.$this->field." ".$this->direction;
    }
}