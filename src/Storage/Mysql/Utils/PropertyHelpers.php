<?php

/**
 * Some helpers for collecting specialized tables (like array or calculated)
 */
namespace Sunhill\ORM\Storage\Mysql\Utils;

use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Objects\Collection;

trait PropertyHelpers
{

    protected function collectClasses()
    {
        $result = [];
        $target = ($this->collection)::class;
        do {
            $result[] = $target;
            $target = get_parent_class($target);
        } while (($target <> PropertiesCollection::class) && ($target <> Collection::class));

        return $result;
    }
    
}