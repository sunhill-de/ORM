<?php

/**
 * @file CollectionManager.php
 * Provides the CollectionManager class for accessing information about the orm collections
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-06-25
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/CollectionManagerTest.php 
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Managers\Exceptions\CollectionClassDoesntExistException;
use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Managers\Exceptions\IsNotACollectionException;

class CollectionManager 
{
    
    public function loadCollection(string $class, int $id)
    {
        if (!class_exists($class)) {
            throw new CollectionClassDoesntExistException("The given class '$class' doesn't exists.");
        }
        if (!is_a($class, Collection::class, true)) {
            throw new IsNotACollectionException("The given class '$class' is not a collection.");
        }
        $object = new $class();
        $object->load($id);
        
        return $object;
    }
    
    public function deleteCollection(string $class, int $id)
    {
        
    }
}
