<?php

namespace Sunhill\ORM\Managers\ClassQuery;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Sunhill\ORM\Query\ConditionBuilder;
use Sunhill\ORM\Facades\Classes;

class ClassConditionBuilder extends ConditionBuilder
{

    /**
     * Returns true when the given class ($entry) has a property of the given type ($value)
     * This returns true even for inherited properties
     * @param unknown $entry
     * @param unknown $value
     * @return bool
     */
    protected function matchHasType($entry, $value): bool
    {
        $properties = Classes::getPropertiesOfClass($entry->name);
        foreach ($properties as $property) {
            if ($property['type'] == $value::getType()) {
                return true;
            }
        }
        return false;
    }
    
    protected function matchHasOwnType($entry, $value): bool
    {
        $properties = Classes::getNamespaceOfClass($entry->name)::getPropertyDefinition();
        foreach ($properties as $property) {
            if ($property::getType() == $value::getType()) {
                return true;
            }
        }
        return false;        
    }
    
    protected function matchesName($test, $target): bool
    {
        if (strpos($test,'%') !== false) {
            $test = str_replace('%','*',$test);
            if (Str::is($test, $target)) {
                return true;
            }
        }
        if ($target == $test) {
            return true;
        }        
        return false;
    }
    
    protected function matchHasName($entry, $value): bool
    {
        foreach ($entry->properties as $property) {
            if ($this->matchesName($value, $property['name'])) {
                return true;
            }
        }
        return false;
    }
    
    protected function matchHasOwnName($entry, $value): bool
    {
        $properties = Classes::getNamespaceOfClass($entry->name)::getPropertyDefinition();
        foreach ($properties as $property) {
            if ($this->matchesName($value, $property->getName())) {
                return true;
            }
        }
        return false;
    }
    
    protected function matchPropertyCondition($entry, $relation, $value): bool
    {
        switch ($relation) {
            case 'has type':
                return $this->matchHasType($entry, $value);
            case 'has own type':
                return $this->matchHasOwnType($entry, $value);
            case 'has name':
                return $this->matchHasName($entry, $value);
            case 'has own name':
                return $this->matchHasOwnName($entry, $value);                
        }
        return false;        
    }
    
    protected function matchIsParent($entry, $value): bool
    {
        $classes = Classes::getInheritanceOfClass($value);
        return in_array($entry->name, $classes);
    }
    
    protected function matchIsDirectParent($entry, $value): bool
    {
        return Classes::getParentOfClass($value) == $entry->name;        
    }
    
    protected function matchHasParent($entry, $value): bool
    {
        if ($entry->name == 'object') {
            return false; // object has no parents
        }
        $classes = Classes::getInheritanceOfClass($entry->name);
        return in_array($value, $classes);
    }
    
    protected function matchHasDirectParent($entry, $value): bool
    {
        return $value == $entry->parent;
    }
    
    protected function matchParentCondition($entry, $relation, $value): bool
    {
        switch ($relation) {
            case 'is':
                return $this->matchIsParent($entry, $value);
            case 'is direct':
                return $this->matchIsDirectParent($entry, $value);
            case 'has':
                return $this->matchHasParent($entry, $value);
            case 'has direct':
                return $this->matchHasDirectParent($entry, $value);
        }
        return false;
    }
    
    protected function matchCondition($entry, $condition): bool
    {
        switch ($condition->key) {
            case 'property':
                return $this->matchPropertyCondition($entry, $condition->relation, $condition->value);
            case 'parent':
                return $this->matchParentCondition($entry, $condition->relation, $condition->value);
            default:    
                return parent::matchCondition($entry, $condition);
        }
    }
    
    
}
