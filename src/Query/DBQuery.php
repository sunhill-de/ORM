<?php

/**
 * @file DBQuery.php
 * Provides the abstract query class for queries on databases
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-31
 * Localization: not necessary
 * Documentation: complete
 * 
 * 
 */

namespace Sunhill\ORM\Query;

use Illuminate\Support\Collection;

abstract class DBQuery extends BasicQuery
{
    
    protected $query;
    
    public function __construct()
    {
        $this->query = $this->getBasicTable();    
    }
    
    abstract protected function getBasicTable();
    
    protected function handleOrder()
    {
        if ($this->order_key !== 'none') {
            $this->query = $this->query->orderBy($this->order_key,$this->order_direction);
        }        
    }
    
    protected function handleOffset()
    {
        if (isset($this->offset)) {
            $this->query = $this->query->offset($this->offset);
        }
    }
    
    protected function handleLimit()
    {
        if (isset($this->limit)) {
            $this->query = $this->query->limit($this->limit);
        }        
    }
    
    protected function finalizeQuery()
    {        
        $this->handleOrder();
        $this->handleOffset();
        $this->handleLimit();
        return $this->query;
    }
    
    protected function findWhereMethod(string $key): string
    {
        if (isset($this->keys[$key])) {
            return $this->keys[$key];
        }
        return '';
    }
    
    protected function handleStrSearch($connection,$key, $search)
    {
        $this->query->$connection($key,'like',$search);
    }
    
    protected function handleInStatement($connection, $key, $value)
    {
        $connection .= 'in';
        $this->query->$connection($key, $value);
    }
    
    protected function handleNumericField(string $connection, $key, $relation, $value)
    {
        if (is_null($value)) {
            $value = $relation;
            $relation = '=';
        }
       switch ($relation) {
            case '=':
            case '==':
            case '!=':
            case '<>':
            case '<':
            case '<=':
            case '>':
            case '>=':
                $this->query->$connection($key, $relation, $value);
                break;
            case 'in':
                $this->handleInStatement($connection, $key, $value);
                break;
            default:
                throw new NotAllowedRelationException("The relation '$relation' is not allowed in this context.");
        }
    }
    
    protected function handleStringField(string $connection, $key, $relation, $value)
    {
        if (is_null($value)) {
            $value = $relation;
            $relation = '=';
        }
        switch ($relation) {
            case '=':
            case '==':
            case '!=':
            case '<>':
            case '<':
            case '<=':
            case '>':
            case '>=':
                $this->query->$connection($key, $relation, $value);
                break;
            case 'in':    
                $this->handleInStatement($connection, $key, $value);
                break;
            case 'begins with':
                $this->handleStrSearch($connection,$key, $value.'%');
                break;
            case 'end with':
                $this->handleStrSearch($connection,$key, '%'.$value);
                break;
            case 'contains':
                $this->handleStrSearch($connection,$key, '%'.$value.'%');
                break;
            default:
                throw new NotAllowedRelationException("The relation '$relation' is not allowed in this context.");
        }
    }
    
    protected function handleWhere(string $connection, $key, $relation, $value)
    {
        if ($key instanceof \Closure) {
            $this->query->$connection($key);
            return;
        }
        $method = $this->findWhereMethod($key);
        
        if (empty($method)) {
            throw new UnknownFieldException("There is no field named '$key'");
        }
        if (!method_exists($this, $method)) {
            throw new QueryException("Method '$method' doesn't exist.");
        }
        
        $this->$method($connection, $key, $relation, $value);        
    }
    
    public function where($key, $relation = null, $value = null): BasicQuery
    {
        $this->handleWhere('where',$key, $relation, $value);
        return $this;
    }
    
    public function orWhere($key, $relation = null, $value = null): BasicQuery
    {
        $this->handleWhere('orWhere',$key, $relation, $value);
        return $this;
    }
    
    public function whereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->handleWhere('whereNot',$key, $relation, $value);
        return $this;
    }
    
    public function orWhereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->handleWhere('orWhereNot',$key, $relation, $value);
        return $this;
    }
    
    protected function execute()
    {
        switch ($this->target) {
            case 'count':
                return $this->finalizeQuery()->count();
            case 'first':
                return $this->finalizeQuery()->first();
            case 'get':
                return $this->finalizeQuery()->get();
            default:
                throw new InvalidTargetException("'".$this->target."' is not a valid target");
        }
    }
    
    
}
