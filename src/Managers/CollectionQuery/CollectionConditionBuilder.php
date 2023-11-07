<?php

namespace Sunhill\ORM\Managers\CollectionQuery;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Sunhill\ORM\Query\ConditionBuilder;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Collections;

class CollectionConditionBuilder extends ConditionBuilder
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
        $properties = Collections::getPropertiesOfClass($entry->name);
        foreach ($properties as $property) {
            if ($property['type'] == $value::getType()) {
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
    
    protected function matchPropertyCondition($entry, $relation, $value): bool
    {
        switch ($relation) {
            case 'has type':
                return $this->matchHasType($entry, $value);
            case 'has name':
                return $this->matchHasName($entry, $value);
        }
        return false;        
    }
    
    protected function matchCondition($entry, $condition): bool
    {
        switch ($condition->key) {
            case 'property':
                return $this->matchPropertyCondition($entry, $condition->relation, $condition->value);
            default:    
                return parent::matchCondition($entry, $condition);
        }
    }
    
    
}
