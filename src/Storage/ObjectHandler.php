<?php

namespace Sunhill\ORM\Storage;


use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\Property;

abstract class ObjectHandler extends CollectionHandler
{
    
    public function __construct(public $storage) {}
    
    protected function doRun()
    {
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy); // Remove object
        
        $this->handleObject();
        foreach ($hirarchy as $class) {
            $this->handleClass(Classes::getNamespaceOfClass($class));
        }
    }
    
    /**
     * Returns the name of the extra table (just basic name + underscore + fieldname)
     */
    protected function getExtraTableName(Property $property)
    {
        return $property->getOwner()::getInfo('table').'_'.$property->getName();
    }
    
 }