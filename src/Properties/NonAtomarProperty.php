<?php
/**
 * @file NonAtomarProperty.php
 * Defines a property that does consist of other properties
 * Lang de,en
 * Reviewstatus: 2023-05-08
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Interfaces\InteractsWithStorage;
use Sunhill\ORM\Properties\Exceptions\PropertyException;

abstract class NonAtomarProperty extends Property
{
    
    protected function checkLoadingState()
    {
        // Non abstract because perhaps sometimes it just doesn't have something to do    
    }
    
    abstract public function hasProperty(string $name): bool;
    abstract public function getProperty(string $name): Property;
    abstract public function getProperties(): array;
    abstract public function getAllProperties(): array;
    
    /**
     * Searches for a property with the given name. If there is one, return its value. If not pass it to the parent __get method
     * @param $name string The name of the unknown member variable
     */
    public function &__get($name)
    {
        $this->checkLoadingState();
        if ($this->hasProperty($name)) {
            return $this->getProperty($name)->getValue();
        } else {
            if ($name == 'id') {
                $id = $this->getID();
                return $id;
            }
        }
        throw new PropertyException("The property '$name' does not exist.");
    }
 
    protected function handleAtomarProperty(AtomarProperty $property, $value)
    {
        $property->setValue($value);        
    }
    
    protected function handleNonAtomarProperty(NonAtomarProperty $property, $value)
    {
        
    }
    
    protected function handleUnknownProperty(string $name, $value)
    {
        return false;    
    }
    
    /**
     * Searches for a property with the given name. If there is one, set its value. if not call handleUnknownProperty()
     * @param $name string The name of the unknown member variable
     * @param $value void The valie for this member variable
     */
    public function __set(string $name, $value)
    {
        if ($this->hasProperty($name)) {
            if ($this->getReadonly()) {
                throw new PropertyException(__("Property ':name' was changed in readonly state.",['name'=>$name]));
            } 
            $this->checkLoadingState(); // Is this object only preloaded?
            $property = $this->getProperty($name);
            if (is_a($property, AtomarProperty::class)) {
                $this->handleAtomarProperty($property, $value);
                return;
            }
            if (is_a($property, NonAtomarProperty::class)) {
                $this->handleNonAtomarProperty($property, $value);
                return;
            }
            throw new PropertyException('Cant handle this property for writing.');
        } else if (!$this->handleUnknownProperty($name,$value)){
            throw new PropertyException(__("Unknown property ':name'",['name'=>$name]));
        }
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\Property::passItemRequest()
     */
    protected function passItemRequest(string $name, array $path)
    {
        if (!$this->hasProperty($name)) {
            return false;
        }
        $this->checkLoadingState();
        $property = $this->getProperty($name);
        
        return $property->requestItem($path);
    }
 
    protected function getMyOffer()
    {
        return array_keys($this->getAllProperties());
    }
        
    protected function passOfferRequest(string $name, array $path)
    {
        if (!$this->hasProperty($name)) {
            return false;
        }
        $this->checkLoadingState();
        $property = $this->getProperty($name);
        
        return $property->requestOffer($path);
    }
    
}