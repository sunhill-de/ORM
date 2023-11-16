<?php

namespace Sunhill\ORM\InfoMarket;

use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;

abstract class MarketeerBase extends NonAtomarProperty
{
    
    protected $entries;
    
    protected static $type = 'array';
    
    protected function addEntry(string $name, $entry)
    {
        if (is_null($this->entries)) {
            $this->entries = [];
        }
        $this->entries[$name] = $entry;
    }

    abstract public function hasProperty(string $name): bool;
    abstract public function getProperty(string $name): Property;
    abstract public function getProperties(): array;
    
    public function getAllProperties(): array
    {
        return $this->getProperties();    
    }
    
}