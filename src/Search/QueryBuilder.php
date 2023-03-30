<?php

/**
 * @file QueryBuilder.php
 * Provides the QueryBuilder class
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

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Utils\ObjectList;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Objects;

class QueryBuilder 
{

    protected $query_parts = [];
    
    protected $calling_class;
    
    protected $used_tables = array();
    
    protected $next_table = 'a';
    
    /**
     * Creates a new query object and passes the calling object class over
     */
    public function __construct(string $classname = '') 
    {
        if (!empty($classname)) {
            $this->setCallingClass($classname);
        }
    }
    
    /**
     * Since a search is initiazied by a specific class, the class is set here 
     * @param unknown $calling_class
     * @return \Sunhill\ORM\Search\QueryBuilder
     */
    public function setCallingClass($calling_class) 
    {
        $this->calling_class = $calling_class;
        return $this;
    }

    /**
     * Returns the calling class
     * @return unknown
     */
    public function get_calling_class() 
    {
        return $this->calling_class;    
    }
    
    /**
     * Returns the part of the query identified by $part_id
     * @param string $part_id
     * @return string|unknown
     */
    protected function getQueryPart(string $part_id) 
    {
        return isset($this->query_parts[$part_id])?$this->query_parts[$part_id]->getQueryPart():'';
    }
    
    /**
     * Sets a new query part identified by $part_id
     * @param string $part_id
     * @param QueryAtom $part
     */
    protected function setQueryPart(string $part_id,QueryAtom $part,$connection = null) 
    {
        if (!isset($this->query_parts[$part_id])) {
            // This part is not set yet, so just set it
            $this->query_parts[$part_id] = $part;
        } else {
            // This part is already set, so decide what to do
            if ($part->isSingleton()) {
                // replace a singleton
                $this->query_parts[$part_id] = $part;
            } else {
                $this->query_parts[$part_id]->link($part,$connection);
            }
        }
    }
    
    /**
     * Enters the passed table into the used_tables array and returns the given letter
     * @param string $table_name
     * @return string
     */
    public function getTable(string $table_name) 
    {
        if (!isset($this->used_tables[$table_name])) {
            $this->used_tables[$table_name] = $this->next_table++;
        }         
        return $this->used_tables[$table_name];
    }
    
    protected function getWherePart($field, $relation, $value) 
    {
        $property = ($this->calling_class)::getPropertyObject($field);
        if (is_null($property)) {
            throw new QueryException(__("The field ':field' is not found.",['field'=>$field]));
        }
        if (! $property->getSearchable()) {
            throw new QueryException(__("The field ':field' is not searchable.",['field'=>$field]));
        }
        switch ($property->type) {
            case 'tags':
                $part = new QueryWhereTag($this,$property,$relation,$value);
                break;
            case 'attribute_char':
            case 'attribute_float':
            case 'attribute_int':
            case 'attribute_float':
                $part = new QueryWhere_attribute($this,$property,$relation,$value);
                break;
            case 'arrayOfObjects':
                $part = new QueryWhereArrayOfObjects($this,$property,$relation,$value);
                break;
            case 'arrayOfStrings':
                $part = new QueryWhereArrayOfStrings($this,$property,$relation,$value);
                break;
            case 'varchar':
                $part = new QueryWhereString($this,$property,$relation,$value);
                break;
            case 'object':
                $part = new QueryWhereObject($this,$property,$relation,$value);
                break;
            case 'calculated':
                $part = new QueryWhereCalculated($this,$property,$relation,$value);
                break;
            default:
                $part = new QueryWhereSimple($this,$property,$relation,$value);
                break;
        }
        return $part;
    }
    
    public function where($field, $relation, $value = null) 
    {
        $part = $this->getWherePart($field,$relation,$value);
        $this->setQueryPart('where',$part,'and');
        return $this;
    }
    
    public function orWhere($field, $relation, $value = null) 
    {
        $part = $this->getWherePart($field,$relation,$value);
        $this->setQueryPart('where',$part,'or');
        return $this;
    }
    
    public function orderBy($field, $asc = true) 
    {    
        if ($field == 'id') {
            return $this; // Do nothing
        }
        $property = ($this->calling_class)::getPropertyObject($field);
        if (is_null($property)) {
            throw new QueryException(__("The field ':field' is not found.",['field'=>$field]));
        }
        $this->setQueryPart('order', new QueryOrder($this,$property,$asc));
        return $this;
    }
    
    public function limit($delta, $limit) 
    {
        $this->setQueryPart('limit', new QueryLimit($this,$delta,$limit));
        return $this;
    }
    
// ****************** Query finalizations ***********************    
    /**
     * Returns the used tables for this query. All tables are joined as inner joins
     * @return string
     */
    protected function getTables() 
    {
        $first = true;
        $result = ' from ';
        foreach ($this->used_tables as $table_name => $alias) {
            if (!$first) {
                $result .= ' inner join ';
            } else {
                $master_alias = $alias;
            }
            $result .= $table_name.' as '.$alias;
            if (!$first) {
                $result .= " on $alias.id = $master_alias.id";
            }
            $first = false;
        }
        return $result;
    }
    
    /**
     * Assembles the queryparts togeteher and return the pure query-string
     * @return string
     */
    protected function finalize() 
    {
        $query_str =
        $this->getQueryPart('target').
        $this->getTables().
        $this->getQueryPart('where').
        $this->getQueryPart('group').
        $this->getQueryPart('order').
        $this->getQueryPart('limit');
 
        return $query_str;
        
    }
    
    protected function prepareQuery(bool $dump) 
    {
        $query_str = $this->finalize();
        if ($dump) {
            return $query_str;
        } else {
            return $this->executeQuery($query_str);
        }
    }
    
    /**
     * Returns all result of this query
     */
    public function get(bool $dump = false) 
    {
        $this->setQueryPart('target', new QueryTargetID($this));
        return $this->postprocessResults($this->prepareQuery($dump));
    }
    
    /**
     * Returns the count of entries of this query
     * @param bool $dump
     * @return string|NULL|\Sunhill\ORM\Search\unknown|NULL[]
     */
    public function count(bool $dump = false) 
    {
        $this->setQueryPart('target', new QueryTargetCount($this));
        $result = $this->prepareQuery($dump);
        if ($dump) {
            return $result;
        } else {
            return $result[0]->count;
        }
    }
    
    /**
     * Returns the first entry of the query
     * @deprecated Should be replaced by ->firstID() or ->load()
     * @param bool $dump
     * @return \Sunhill\ORM\Search\unknown
     */
    public function first(bool $dump = false) 
    {
        return $this->firstID($dump);
    }
    
    /**
     * Alias of ->first()
     * @param bool $dump
     * @return \Sunhill\ORM\Search\unknown
     */
    public function firstID(bool $dump = false) 
    {
        $this->setQueryPart('target', new QueryTargetID($this));
        $this->setQueryPart('limit', new QueryLimit($this,0,1));
        $result = $this->prepareQuery($dump);
        if ($dump) {
            return $result;
        } else {
            if (empty($result)) {
                return null;
            } else {
                return $result[0]->id;
            }
        }
    }
    
    /**
     * returns the loaded first entry of the query. If it doesn't exist it raises an exception
     * @throws QueryException
     * @return NULL
     */
    public function load() 
    {
        $result = $this->loadIfExists();        
        if (empty($result)) {
            throw new QueryException("load() expects at least one result. Non returned");
        }
        return $result;
    }
    
    /**
     * return the laoded first entry of the query or null if it doesn't exist
     * @return unknown|NULL
     */
    public function loadIfExists() 
    {
        $this->setQueryPart('target', new QueryTargetID($this));
        $this->setQueryPart('limit', new QueryLimit($this,0,1));
        $result = $this->prepareQuery(false);
        if (!empty($result)) {
            return Objects::load($result[0]->id);       
        } else {
            return null;
        }
    }
    
    /**
     * Converts the database result into a ObjectList 
     * @param unknown $result
     * @return unknown
     */
    protected function postprocessResults($result) 
    {
        if (is_string($result)) {
            return $result; // We requested a dump
        } else {
            $return = new ObjectList();
            foreach ($result as $entry) {
                $return[] = $entry->id;
            }
            return $return;
        }
    }
    
// ********************* Query-Management  ****************************
    protected function executeQuery(string $querystr) 
    {
        return DB::select($querystr);
    }
    
}