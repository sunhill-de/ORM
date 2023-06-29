<?php

/**
 * Some helpers for collecting specialized tables (like array or calculated)
 */
namespace Sunhill\ORM\Storage\Mysql\Utils;

use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\Schema;

trait PropertyHelpers
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
    
}