<?php

namespace Sunhill\ORM\Storage;

use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Objects\PropertyCollection;

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
        $this->handleClass($this->storage->getCaller()::class);
    }
    
    /**
     * Returns the property with the name $name
     * @param Property $name
     */
    protected function getProperty(string $name): Property
    {
        return $this->storage->getCaller()->getProperty($name);
    }
    
    /**
     * Returns the classname (without namespace) of the given property
     */
    protected function getPropertyClassName(Property $property): string
    {
        $namespace = explode('\\',$property::class);
        return array_pop($namespace);        
    }
    
    /**
     * Runs through all properties of the given collection and calls the according
     * method from HandlesProperties
     */
    protected function handleClass(string $class)
    {
        $properties = $class::getPropertyDefinition();
        foreach ($properties as $name => $property) {
            $method = 'handle'.$this->getPropertyClassName($property);
            $this->$method($property);
        }
    }
    
    protected function iterateStorage()
    {
        
    }
 
    /**
     * Returns the name of the extra table (just basic name + underscore + fieldname)
     */
    protected function getExtraTableName(Property $property)
    {
        return $property->getOwner()::getInfo('table').'_'.$property->getName();
    }
    
}