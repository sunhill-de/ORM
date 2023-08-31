<?php

namespace Sunhill\ORM\InfoMarket;

use Sunhill\ORM\Properties\Property;

class Market extends Marketeer
{
            
    public function hasProperty(string $name): bool
    {
        
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

    public function installMarketeer(string $name, string $marketeer)
    {
        $this->addEntry($name, $marketeer);  
    }
    
    /**
     * gets the value and metadata of the given item $path, checks the access rights and returns it in the wanted format
     * @param $path string: The dot separated path to the item (or branch)
     * @param $credentials string: The current credentials of the user
     * @param $format string ('json', 'object', 'array') The desired output format
     */
    public function getItem(string $path, string $credentials = 'anybody', string $format = 'json')
    {
    }
    
    /**
     * gets the value and metadata of the given item $path, checks the access rights and sets the value
     * @param $path string: The dot separated path to the item (or branch)
     * @param $value : The value to set
     * @param $credentials string: The current credentials of the user
     * @param $format string ('json', 'object', 'array') The desired output format
     */
    public function setItem(string $path, $value, string $credentials = 'anybody', string $format = 'json')
    {
    }
        
}