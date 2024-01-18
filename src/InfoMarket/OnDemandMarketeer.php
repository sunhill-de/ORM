<?php

namespace Sunhill\ORM\InfoMarket;

use Sunhill\ORM\Properties\NonAtomarProperty;
use Sunhill\ORM\Properties\Property;

abstract class OnDemandMarketeer extends MarketeerBase
{

    abstract protected function initializeMarketeer();
    
    protected function checkInitialization()
    {
        if (is_null($this->entries)) {
            $this->entries = [];
            $this->initializeMarketeer();
        }
    }
    
    public function hasProperty(string $name): bool
    {
        $this->checkInitialization();
        return array_key_exists($name, $this->entries);
    }
    
    public function getProperty(string $name): Property
    {
        $this->checkInitialization();
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
        $this->checkInitialization();
        return $this->entries;
    }

    public function getValue()
    {
        $this->checkInitialization();
        return $this->entries; 
    }
}