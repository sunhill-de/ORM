<?php

namespace Sunhill\ORM\Query;

use Illuminate\Support\Collection;

abstract class BasicQuery
{

    protected $offset;
    
    protected $limit;
    
    protected $order_key = 'none';
    
    protected $order_direction = 'asc';
    
    protected $conditions = [];
    
    protected $condition_builder;
    
    protected $target;
    
    public function __construct()
    {
        $this->condition_builder = new ConditionBuilder();    
    }
    
    protected function targetCount()
    {
        $this->target = 'count';
    }
    
    public function count(): int
    {
        $this->targetCount();
        return $this->execute();
    }
    
    protected function targetFirst()
    {
        $this->target = 'first';
    }
    
    public function first()
    {
        $this->targetFirst();
        return $this->execute();
    }
    
    protected function targetGet()
    {
        $this->target = 'get';
    }
    
    public function get(): Collection
    {
        $this->targetGet();
        return $this->execute();
    }
    
    public function offset(int $offset): BasicQuery
    {
        $this->offset = $offset;
        return $this;
    }
    
    public function limit(int $limit): BasicQuery
    {
        $this->limit = $limit;
        return $this;
    }
    
    public function where($key, $relation = null, $value = null): BasicQuery
    {
        $this->condition_builder->where($key, $relation, $value);
        return $this;        
    }
    
    public function orWhere($key, $relation = null, $value = null): BasicQuery
    {
        $this->condition_builder->orWhere($key, $relation, $value);
        return $this;
    }
    
    public function whereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->condition_builder->whereNot($key, $relation, $value);
        return $this;        
    }
    
    public function orWhereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->condition_builder->orWhereNot($key, $relation, $value);
        return $this;
    }
    
    public function orderBy($key, $direction = 'asc'): BasicQuery
    {
        $direction = strtolower($direction);
        if (!in_array($direction,['asc','desc'])) {
            throw new InvalidOrderException("'$direction' is not a valid order direction.");
        }
        $this->order_key = $key;
        $this->order_direction = $direction;
        return $this;
    }
    
    abstract protected function execute();    
    
    protected function unaryCondition($key)
    {
        throw new NoUnaryConditionException("This query doesn't define a unary condition");    
    }
        
    protected function binaryCondition($combine, $key, $relation, $value)
    {
        $entry = new \StdClass();
        $entry->combine = $combine;
        $entry->key = $key;
        $entry->relation = $relation;
        $entry->value = $value;
        $this->conditions[] = $entry;
    }
    
    protected function arrayToCollection(array $input): Collection
    {
        return collect($input);
    }
}