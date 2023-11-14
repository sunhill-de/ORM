<?php

namespace Sunhill\ORM\InfoMarket;

use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;

class Marketeer extends NonAtomarProperty
{
    
    protected $entries = [];

    protected static $type = 'array';
    
    protected function addEntry(string $name, $entry)
    {
         $this->entries[$name] = $entry;
    }
    
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
        }
        return $entry;
    }
    
    public function getProperties(): array
    {
        return $this->entries;
    }
    
    public function getAllProperties(): array
    {
        return $this->entries;        
    }

}