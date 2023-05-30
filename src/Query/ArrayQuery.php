<?php

/**
 * @file ArrayQuery.php
 * Provides an abstract query class for queries on arrays
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-23
 * Localization: not necessary
 * Documentation: complete
 * 
 * 
 */

namespace Sunhill\ORM\Query;

abstract class ArrayQuery extends BasicQuery
{
    
    protected $allowed_order_keys = ['none','name'];
    
    abstract protected function getRawData();

    protected $target;
    
    protected function targetCount()
    {
        $this->target = 'count';
    }
    
    protected function targetFirst()
    {
        $this->target = 'first';
    }
    
    protected function targetGet()
    {
        $this->target = 'get';
    }

    protected function matches(\StdClass $entry): bool
    {
        return $this->condition_builder->testValue($entry);
    }
    
    protected function filterList($list)
    {
        if ($this->condition_builder->empty()) {
            return $list;            
        }
        $result = [];
        foreach ($list as $entry) {
            if ($this->matches($entry)) {
                $result[] = $entry;
            }
        }
        return collect($result);
    }
    
    protected function orderList($list)
    {
        if ($this->order_key == 'none') {
            return $list;
        }
        if (!in_array($this->order_key,$this->allowed_order_keys)) {
            throw new InvalidOrderException("'".$this->order_key."' is now an allowed order key.");
        }
        $key = $this->order_key;
        $dir = $this->order_direction;
        return $list->sort(function($key1,$key2) use ($key, $dir) {
            if ($key1->$key == $key2->$key) {
                return 0;
            } else if ($dir == 'asc') {
                return ($key1->$key < $key2->$key) ? -1 : 1;
            } else {
                return ($key1->$key > $key2->$key) ? -1 : 1;
            }
        });
    }
    
    protected function sliceList($list)
    {
        $offset = is_null($this->offset)?0:$this->offset;
        $limit  = is_null($this->limit)?10000000000:$this->limit;
        return $list->slice($offset, $limit);
    }
    
    protected function executeQuery()
    {
        $list = $this->arrayToCollection(array_values(array_map(function($element) {
            $result = new \StdClass();
            foreach ($element as $key => $value) {
                $result->$key = $value;
            }
            return $result;
        },$this->getRawData())));
            $list = $this->filterList($list);
            $list = $this->orderList($list);
            return $this->sliceList($list);
    }
    
    protected function executeCount()
    {
        $list = $this->executeQuery();
        return count($list);
    }
    
    protected function executeFirst()
    {
        return $this->executeQuery()->first();
    }
    
    protected function executeGet()
    {
        return $this->executeQuery();
    }
    
    protected function execute()
    {
        switch ($this->target) {
            case 'count':
                return $this->executeCount();
            case 'first':
                return $this->executeFirst();
            case 'get':
                return $this->executeGet();
            default:
                throw new InvalidTargetException("'".$this->target."' is not a valid target");
        }
    }
    
    
}
