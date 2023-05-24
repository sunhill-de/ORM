<?php

namespace Sunhill\ORM\Storage;


use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\PropertyCollection;

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
    
 }