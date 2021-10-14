<?php

/**
 * @file QueryWhere.php
 * Provides the QueryWhere class
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
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Search\QueryException;
use Sunhill\ORM\Facades\Classes;

/**
 * An abstract base class for where parts in a query. It provides a check for allowed relations and stores the required field for the query
 * @author klaus
 *
 */
abstract class QueryWhere extends QueryAtom {
    
    protected $field;
    
    protected $relation;
    
    protected $value;
    
    protected $allowed_relations = 
        [
            '='=>'scalar',
            '<'=>'scalar',
            '>'=>'scalar',
            '<='=>'scalar',
            '>='=>'scalar',
            '<>'=>'scalar',
            '!='=>'scalar',
            'in'=>'array'
        ];
    
    protected $alias;
    
    protected $parent_query;
    
    public function __construct(QueryBuilder $parent_query, Property $field, $relation, $value = null) 
    {
        if (is_null($value)) {
            if (!isset($this->allowed_relations[$relation])) {
                $value = $relation;
                $relation = '=';                
            }
        }
        parent::__construct($parent_query);
        if (!$this->isAllowedRelation($relation,$value)) {
            throw new QueryException("'$relation' is not an allowed relation in this context.");
        }
        $this->parent_query = $parent_query;
        $this->relation = $relation;
        $this->value = $value;
        $this->get_table($field);
    }
    
    protected function get_table(Property $field) 
    {
        $this->alias = $this->parent_query->get_table($this->getTableName($field));
        $this->field = $field->getName();        
    }
    
    protected function getTableName(Property $field) 
    {
        return Classes::getTableOfClass($field->getClass());
    }
    
    /**
     * Checks, if this relation is allowed in this context
     * @param string $relation
     * @return boolean true, if its an allowed relation, otherwise false
     */
    protected function isAllowedRelation(string $relation,$value) 
    {
        if (!isset($this->allowed_relations[$relation])) {
            // Is this relation allowed at all?
            return false;
        }
        switch ($this->allowed_relations[$relation]) {
            case 'scalar':
                return is_scalar($value);
                break;
            case 'array':
                return is_array($value);
                break;
            case 'unary':
                return true;
            case 'object':
                return is_object($value)||is_int($value)||is_null($value);
        }
    }
    
    protected function getQueryPrefix() 
    {
        if (is_null($this->prev)) {
            return ' where';
        } else {
            return '';
        }
    }
    
    protected function getThisWherePart() 
    {
        if ($this->relation == 'in') {
            $result = $this->getQueryPrefix().' '.$this->alias.'.'.$this->field.' in (';
            $first = true;
            foreach ($this->value as $value) {
                $result .= ($first?'':',').$this->escape($value);
                $first = false;
            }
            $result .= ')';
        } else {
            $result = $this->getQueryPrefix().' '.$this->alias.'.'.$this->field.' '.$this->relation." ".$this->escape($this->value);
        }
        return $result;
    }
    
    public function getQueryPart() 
    {
        $result = $this->getThisWherePart();
        if (isset($this->next)) {
            $result .= ' '.$this->connection.=$this->next->getQueryPart();
        }
        return $result;
    }
    
    protected function escape(string $sample) 
    {
        return DB::connection()->getPdo()->quote($sample);
    }
}