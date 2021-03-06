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
    public function __construct($properties)
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
        return $this->executeFilters($field,$relation,$condition,true);
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
        return $this->executeFilters($field,$relation,$condition,false);
    }    
  
    public function groupBy(string $condition)
    {
        $result = [];
        foreach ($this->current as $property)
        {
           $method = $this->getGetter($condition);
           $group = $property->$method();
           if (isset($result[$group])) {
               $result[$group][] = $property;
           } else {
               $result[$group] = [$property];
           }
        }
        $this->current = $result;
        return $this;
    }
    
    /**
     * A common executor for where and whereNot
     * @param unknown $field
     * @param unknown $relation
     * @param unknown $condition
     * @param unknown $negate
     * @return \Sunhill\ORM\PropertyQuery\PropertyQuery
     */
    protected function executeFilters($field,$relation,$condition,$negate)
    {
        if (is_null($condition)) {
            $condition = $relation;
            $relation = '=';
        }
        $result = [];
        foreach ($this->current as $property)
        {
            $match = $this->matches($property,$field,$relation,$condition);
            if ($match == $negate) {
                $result[] = $property;
            }
        }
        $this->current = $result;
        return $this;        
    }
    
    protected function getGetter($field)
    {
        return 'get'.ucfirst(strtolower($field));
    }
    
    protected function matches($property,$field,$relation,$condition)
    {
        $method = $this->getGetter($field);
        $result = $property->$method();
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
