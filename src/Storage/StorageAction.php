<?php
/**
 * @file StorageBase.php
 * The basic class for storages (at the moment there is only StorageMySQL)
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Properties\Property;
use Illuminate\Testing\Assert as PHPUnit;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Storage\Exceptions\PropertyNotFoundException;

/**
 * 
 * @author lokal
 *
 */
abstract class StorageAction
{
    
    protected $collection;
    
    protected $additional;
        
    public function setCollection(PropertiesCollection $collection)
    {
        $this->collection = $collection;    
    }
    
    public function getCollection(): PropertiesCollection
    {
        return $this->collection;    
    }

    public function setAdditional($additional)
    {
        $this->additional = $additional;
        return $this;
    }
    
    abstract public function run();

    protected function checkAndGetProperty(string $name)
    {
        if (empty($property = $this->collection->getProperty($name))) {
            throw new PropertyNotFoundException("The property '$name' was not found.");
        }
        return $property;
    }
    
    protected function setPropertyValue(string $name, $value)
    {
        $property = $this->checkAndGetproperty($name);
        $property->loadValue($value);        
    }
    
    protected function getPropertyValue(string $name)
    {
        $property = $this->checkAndGetproperty($name);
        return $property->getValue();
    }
    
    protected function getPropertyShadow(string $name)
    {
        $property = $this->checkAndGetproperty($name);
        return property->getShadow();
    }
    
    protected function setPropertyShadow(string $name, $value)
    {
        $property = $this->checkAndGetproperty($name);
        $property->setShadow($value);
    }
    
    protected function isDynamicProperty($property): bool
    {
        return $this->collection->isDynamicProperty($property->name);
    }
    
    protected function mapProperty($property)
    {
        if ($this->isDynamicProperty($property)) {
            $this->handleAttribute($property);
            return;
        }
        if (is_a($property,Property::class)) {
            $type_parts = explode('\\',$property::class);            
        } else {
            $type_parts = explode('\\',$property->type);
        }
        $type = 'handle'.array_pop($type_parts);
        $this->$type($property);
    }
    
    protected function runProperties(bool $local_only = false)
    {
        $properties = $this->collection->propertyQuery()->get();
        if ($local_only)  {
            $local = ($this->collection)::getPropertyDefinition();
            $properties = $properties->filter(function($value, $key) use ($local) {
                return array_key_exists($value->name, $local);
            });
        } 
        foreach ($properties as $property) {
            $this->mapProperty($property);
        }
    }
}
