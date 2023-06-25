<?php
/**
 * @file CollectionStorer.php
 * Defines the class CollectionStorer. This class is used to store an object/collection to a 
 * storage 
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

use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyMap;

class CollectionStorer extends StorageInteractionBase
{
        
    protected function copyValue($property, $value)
    {
        return $this->storage->createEntity($property->getName(), $this->collection->getInfo('table'))
            ->setValue($value);        
    }

    protected function copyProperty($property)
    {
        return $this->copyValue($property, $property->getValue());    
    }
    
    protected function copyArrayOrMap($property)
    {
        $values = [];
        foreach ($property->getValue() as $key => $element) {
            if ($property->getElementType() == PropertyObject::class) {
                $values[$key] = $element->getID();                
            } else {
                $values[$key] = $element;
            }
        }
        return $this->storage->createEntity($property->getName(), $this->collection->getInfo('table'))
        ->setValue($values);        
    }
    
    public function handlePropertyArray($property)
    {
        $this->copyArrayOrMap($property)->setType(PropertyArray::class)->setElementType($property->getElementType());
    }
    
    public function handlePropertyBoolean($property)
    {   
        $this->copyProperty($property)->setType(PropertyBoolean::class);
    }
    
    public function handlePropertyCalculated($property)
    {
        $this->copyProperty($property)->setType(PropertyCalculated::class);
    }
    
    public function handlePropertyCollection($property)
    {
        $this->copyValue($property, $property->getID());        
    }
    
    public function handlePropertyDate($property)
    {
        $this->copyProperty($property)->setType(PropertyDate::class);
    }
    
    public function handlePropertyDateTime($property)
    {
        $this->copyProperty($property)->setType(PropertyDatetime::class);
    }
    
    public function handlePropertyEnum($property)
    {        
        $this->copyProperty($property)->setType(PropertyEnum::class);        
    }
    
    public function handlePropertyExternalReference($property)
    {
    }
    
    public function handlePropertyFloat($property)
    {
        $this->copyProperty($property)->setType(PropertyFloat::class);        
    }
    
    public function handlePropertyInformation($property)
    {
    }
    
    public function handlePropertyInteger($property)
    {
        $this->copyProperty($property)->setType(PropertyInteger::class);
    }
    
    public function handlePropertyKeyfield($property)
    {
    }
    
    public function handlePropertyMap($property)
    {
        $this->copyArrayOrMap($property)->setType(PropertyMap::class)->setElementType($property->getElementType());;
    }
    
    public function handlePropertyObject($property)
    {        
        $this->copyValue($property, $property->getValue()->getID())->setType(PropertyObject::class); 
    }
    
    public function handlePropertyTags($property)
    {
    }
    
    public function handlePropertyText($property)
    {
        $this->copyProperty($property)->setType(PropertyText::class);        
    }
    
    public function handlePropertyTime($property)
    {
        $this->copyProperty($property)->setType(PropertyTime::class);
    }
    
    public function handlePropertyVarchar($property)
    {
        $this->copyProperty($property)->setType(PropertyVarchar::class);        
    }
    
    protected function additionalRun()
    {
    }
}