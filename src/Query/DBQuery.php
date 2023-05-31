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
    
    public function where($key, $relation = null, $value = null): BasicQuery
    {
        $this->query->where($key,$relation,$value);
        return $this;
    }
    
    public function orWhere($key, $relation = null, $value = null): BasicQuery
    {
        $this->query->orWhere($key,$relation,$value);
        return $this;
    }
    
    public function whereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->query->whereNot($key,$relation,$value);
        return $this;
    }
    
    public function orWhereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->query->orWhereNot($key,$relation,$value);
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
