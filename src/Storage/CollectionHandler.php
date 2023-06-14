<?php

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Properties\Property;

abstract class CollectionHandler implements HandlesProperties
{
    
    public function __construct(public $storage) {}
    
    public function run()
    {
        $this->prepareRun();
        $this->doRun();
        $this->finishRun();
    }
    
    abstract protected function prepareRun();
    abstract protected function finishRun();
    
    protected function doRun()
    {
        $list = $this->storage->getStorageIDs();
        foreach ($list as $storage_id) {
            $this->handleClass($storage_id);
            $this->handleProperties($storage_id);
        }
    }
    
    /**
     * Returns the classname (without namespace) of the given property
     */
    protected function getPropertyClassName(string $property): string
    {
        $namespace = explode('\\',$property);
        return array_pop($namespace);        
    }
    
    /**
     * Runs through all properties of the given collection and calls the according
     * method from HandlesProperties
     */
    protected function handleClass(string $class)
    {
    }
    
    protected function handleProperties(string $class)
    {
        $properties = $this->storage->getEntitiesOfStorageID($class);
        foreach ($properties as $name => $property) {
            $method = 'handle'.$this->getPropertyClassName($property->type);
            $this->$method($property);
        }        
    }
    
    /**
     * Returns the name of the extra table (just basic name + underscore + fieldname)
     */
    protected function getExtraTableName($property)
    {
        return $property->storage_id.'_'.$property->name;
    }
    
}