<?php

namespace Sunhill\ORM\InfoMarket;

use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;

class Marketeer extends NonAtomarProperty
{
    
    protected $entries = [];
    
    protected function addEntry(string $name, $entry)
    {
         $this->entries[$name] = $entry;
    }
    
    public function hasProperty(string $name): bool
    {
        return array_has_key($name, $this->entries);
    }
    
    public function getProperty(string $name): Property
    {
        
    }
    
    public function getProperties(): array
    {
        
    }
    
    public function getAllProperties(): array
    {
        
    }

}