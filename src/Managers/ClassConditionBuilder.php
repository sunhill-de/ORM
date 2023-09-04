<?php

namespace Sunhill\ORM\Managers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Sunhill\ORM\Query\ConditionBuilder;
use Sunhill\ORM\Facades\Classes;

class ClassConditionBuilder extends ConditionBuilder
{

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
    
    protected function matchPropertyCondition($entry, $relation, $value): bool
    {
        switch ($relation) {
            case 'has type':
                return $this->matchHasType($entry, $value);
            case 'has own type':
                return $this->matchHasOwnType($entry, $value);
        }
        return false;        
    }
    
    protected function matchIsParent($entry, $value): bool
    {
        
    }
    
    protected function matchHasParent($entry, $value): bool
    {
        
    }
    
    protected function matchHasDirectParent($entry, $value): bool
    {
        
    }
    
    protected function matchParentCondition($entry, $relation, $value): bool
    {
        switch ($relation) {
            case 'is':
                return $this->matchIsParent($entry, $value);
            case 'has':
                return $this->matchHasParent($entry, $value);
            case 'has direct':
                return $this->matchHasDirectParent($entry, $value);
        }
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
