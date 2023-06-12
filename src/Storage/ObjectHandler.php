<?php

namespace Sunhill\ORM\Storage;


use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\Property;

abstract class ObjectHandler extends CollectionHandler
{
    
    protected function doRun()
    {
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy); // Remove object
        
        $this->handleObject();
        foreach ($hirarchy as $class) {
            $this->handleClass(Classes::getNamespaceOfClass($class));
        }
    }
    
 }