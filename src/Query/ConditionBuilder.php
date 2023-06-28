<?php

namespace Sunhill\ORM\Query;

use Illuminate\Support\Collection;

class ConditionBuilder
{

    protected $conditions = [];
    
    protected function handleWhere(string $combine, $key, $relation, $value)
    {
        $entry = new \StdClass();
        $entry->combine = $combine;
        
        if ($key instanceof \Closure) {
            if (!is_null($relation) || !is_null($value)) {
                throw new TooManyWhereParametersException("Where conditions with callback may only take one parameter.");
            }
            $entry->callback = new ConditionBuilder();
            $key($entry->callback);
        } else {
            if (is_null($value)) { // When no relation is given assume =
                $value = $relation;
                $relation = '=';
            }
            $entry->key      = $key;
            $entry->relation = $relation;
            $entry->value    = $value;
        }
        $this->conditions[] = $entry;
    }
    
    public function where($key, $relation, $value)
    {
        $this->handleWhere('and', $key, $relation, $value);
        return $this;
    }
    
    public function notWhere($key, $relation, $value)
    {
        $this->handleWhere('and not', $key, $relation, $value);        
        return $this;
    }
    
    public function orWhere($key, $relation, $value)
    {
        $this->handleWhere('or', $key, $relation, $value);        
        return $this;
    }
    
    public function orNotWhere($key, $relation, $value)
    {
        $this->handleWhere('or not', $key, $relation, $value);        
        return $this;
    }
    
    protected function matchCallbackCondition($entry, $condition): bool
    {
        return $condition->callback->testValue($entry);
    }
    
    protected function matchSimpleCondition($key, $relation, $value): bool
    {
        switch ($relation) {
            case '=':
            case '==':
                return $key == $value;
            case '!=':
            case '<>':
                return $key !== $value;
            case '>':
                return $key > $value;
            case '>=':
                return $key >= $value;
            case '<':
                return $key < $value;
            case '<=':
                return $key <= $value;
        }
    }
    
    protected function matchCondition($entry, $condition): bool
    {
        if (isset($condition->callback)) {
            return $this->matchCallbackCondition($entry, $condition);
        }
        $key_field = $condition->key;
        $key = $entry->$key_field;
        $value = $condition->value;
        return $this->matchSimpleCondition($key, $condition->relation, $value);
    }
    
    public function testValue($entry): bool
    {
        $result = null;
        foreach ($this->conditions as $condition) {
            if (is_null($result)) {
                $result = $this->matchCondition($entry, $condition);
            } else {
                switch ($condition->combine) {
                    case 'and': $result = $result && $this->matchCondition($entry, $condition); break;
                    case 'or': $result = $result || $this->matchCondition($entry, $condition); break;
                    case 'and not': $result = $result && !$this->matchCondition($entry, $condition); break;
                    case 'or not': $result = $result || !$this->matchCondition($entry, $condition); break;
                }
            }
        }
        return $result;        
    }
    
    public function empty(): bool
    {
        return empty($this->conditions);
    }
}
