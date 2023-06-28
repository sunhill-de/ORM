<?php
/**
 * @file StorageInteractionBase.php
 * Defines the class StoarageInteractionBase. This class is used to transfer data between 
 * objects/collections and the storage
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-22
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 * PSR-State: complete
 * Tests: none
 */

namespace Sunhill\ORM\Objects\StorageInteraction;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\Interfaces\HandlesProperties;

abstract class StorageInteractionBase implements HandlesProperties
{
    
    protected $storage;
    
    protected $collection;
    
    public function setStorage(StorageBase $storage): StorageInteractionBase
    {
        $this->storage = $storage;
        return $this;
    }
    
    public function setPropertiesCollection(PropertiesCollection $collection): StorageInteractionBase
    {
        $this->collection = $collection;
        return $this;
    }

    public function run()
    {
        $this->runCollection();    
    }
    
    protected function runCollection()
    {
        $this->preRun();
        $properties = $this->collection->getAllProperties();
        $dynamic = $this->collection->getDynamicProperties();
        
        foreach ($properties as $name => $property) {
            if (isset($dynamic[$name])) {
                $this->handleAttribute($property);
            } else {
                $property_class_parts = explode('\\', $property::class);
                $method_name = 'handle'.array_pop($property_class_parts);
                $this->$method_name($property);
            }
        }
        $this->additionalRun();
    }
    
    protected function preRun()
    {
        
    }
    
    abstract protected function additionalRun();
    
    protected function runStorage()
    {
        $properties = $this->storage->getAllEntities();
        
        foreach ($properties as $name => $property) {
            
        }
    }
}