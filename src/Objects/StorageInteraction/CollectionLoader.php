<?php
/**
 * @file CollectionLoader.php
 * Defines the class CollectionLoader. This class is used to load an collection from a 
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

class CollectionLoader extends StorageInteractionBase
{
    
    protected function getEntityValue($property)
    {
        return $this->storage->getEntity($property->getName())->getValue();    
    }
    
    protected function copyValue($property)
    {
        $property->loadValue($this->getEntityValue($property));        
    }
    
    protected function loadArrayOrMap($property, $value)
    {
        if (empty($value)) {
            return;
        }
        if ($property->getElementType() === PropertyObject::class) {
            $property->loadValue(array_map(function($element) {
                return Objects::load($element);
            }, $value));
        } else {
            $property->loadValue($value);
        }
    }
    
    
    public function handlePropertyArray($property)
    {
        $this->loadArrayOrMap($property, $this->getEntityValue($property));
    }
    
    public function handlePropertyBoolean($property)
    {   
        $this->copyValue($property);
    }
    
    public function handlePropertyCalculated($property)
    {
        $this->copyValue($property);
    }
    
    public function handlePropertyCollection($property)
    {
        
    }
    
    public function handlePropertyDate($property)
    {
        $this->copyValue($property);        
    }
    
    public function handlePropertyDateTime($property)
    {
        $this->copyValue($property);        
    }
    
    public function handlePropertyEnum($property)
    {        
        $this->copyValue($property);       
    }
    
    public function handlePropertyExternalReference($property)
    {
        
    }
    
    public function handlePropertyFloat($property)
    {
        $this->copyValue($property);        
    }
    
    public function handlePropertyInformation($property)
    {
        // Do nothing
    }
    
    public function handlePropertyInteger($property)
    {
        $this->copyValue($property);        
    }
    
    public function handlePropertyKeyfield($property)
    {
        // Do nothing
    }
    
    public function handlePropertyMap($property)
    {
        $this->loadArrayOrMap($property, $this->getEntityValue($property));
    }
    
    public function handlePropertyObject($property)
    {
        $id = $this->getEntityValue($property);
        if (is_null($id)) {
            return;
        }
        $property->loadValue(Objects::load($id));
    }
    
    public function handlePropertyTags($property)
    {
        
    }
    
    public function handlePropertyText($property)
    {
        $this->copyValue($property);        
    }
    
    public function handlePropertyTime($property)
    {
        $this->copyValue($property);        
    }
    
    public function handlePropertyVarchar($property)
    {
        $this->copyValue($property);        
    }
    
    protected function additionalRun()
    {
        
    }
}