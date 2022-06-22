<?php

/**
 * @file PropertyQuery.php
 * Provides an convenient method to receive a list of properties
 * Lang en
 * Reviewstatus: 2022-06-22
 * Localization: incomplete
 * Documentation: incomplete
 * Tests: Unit/Objects/PropertyQueryTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\PropertyQuery;

class PropertyQuery
{
    
    protected $current;
  
    /**
     * Due the fact, that the constructor is called by the object, it gets the property list of that object and performs all
     * further processings on this copy
     * @param $properties array of Property: The list of properties of that object
     */
    public function __constructor($properties)
    {
        $this->current = $properties;
    }  

    /**
     * Returns the current list of (maybe filtered) properties
     * @return array of Property
     */
    public function get()
    {
        return $this->current;
    }
  
    /**
     * Filters the current list and removes all entries that does not macth of the given condition
     * @param $field string The name of the Condition (e.g. readonly)
     * @param $relation: if $condition is not gives $relation is assumed to be = and $condition is $relation
     * @param $condition: A condition for the query
     * @return $this
     */
    public function where($field,$relation,$condition=null)
    {
        if (is_null($condition)) {
           $condition = $relation;
           $relation = '=';
        }
        $result = [];
        foreach ($this->current as $property)
        {
            if ($this->matches($property,$field,$relation,$condition)) {
              $result[] = $property;
            }  
        }  
        $this->current = $result;
        return $this;
    }  
  
    /**
     * Filters the current list and removes all entries that does match of the given condition
     * @param $field string The name of the Condition (e.g. readonly)
     * @param $relation: if $condition is not gives $relation is assumed to be = and $condition is $relation
     * @param $condition: A condition for the query
     * @return $this
     */
    public function whereNot($field,$relation,$condition=null)
    {
        if (is_null($condition)) {
           $condition = $relation;
           $relation = '=';
        }
        $result = [];
        foreach ($this->current as $property)
        {
            if (!$this->matches($property,$field,$relation,$condition)) {
              $result[] = $property;
            }  
        }
        $this->current = $result;
        return $this;
    }
  
    protected function matches($property,$field,$relation,$condition)
    {
        $method = 'get'.$field;
        $result = $property->method();
        switch ($relation)
        {
          case '=':
          case '==':
            return $result == $condition;
          case '<':
            return $result < $condition;
          case '>':
            return $result > $condition;
          case '<=':
            return $result <= $condition;
          case '>=':
            return $result >= $condition;
        }    
    }  
}
