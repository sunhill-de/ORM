<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\StorageAction;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Facades\Objects;

/**
 * Helper class with some utilities for mysql tables
 * @author klaus
 *
 */
abstract class MysqlAction extends StorageAction 
{
    
    protected function collectClasses()
    {
        $result = [];
        $properties = $this->collection->propertyQuery()->get();
        foreach ($properties as $property) {
            if (!in_array($property->owner, $result)) {
                $result[] = $property->owner;
            }
        }
        return $result;
    }
    
    protected function setValue($property, $value)
    {
        $name = $property->name;
        $this->collection->getProperty($name)->loadValue($value);        
    }
    
    protected function loadValue($property)
    {
        $name = $property->name;
        $this->setValue($property, $this->data->$name);
    }
 
    protected function getSpecialTableName($property)
    {
        return $property->owner::getInfo('table').'_'.$property->name;
    }
}