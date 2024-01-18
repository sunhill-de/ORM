<?php

namespace Sunhill\ORM\InfoMarket;

use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;

class Marketeer extends MarketeerBase
{
    
    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->entries);
    }
    
    public function getProperty(string $name): Property
    {
        $entry = $this->entries[$name];
        if (is_string($entry)) {
            $entry = new $entry();
            $entry->setActualPropertiesCollection($this);
            $entry->setName($name);
        }
        return $entry;
    }
    
    public function getProperties(): array
    {
        return $this->entries??[];
    }
    
}