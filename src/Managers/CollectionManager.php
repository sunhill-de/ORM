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
    
    protected $collections = [];
    
    protected function checkCollection(string $class)
    {
        $class = $this->searchCollection($class);
        if (!class_exists($class)) {
            throw new CollectionClassDoesntExistException("The given class '$class' doesn't exists.");
        }
        if (!is_a($class, Collection::class, true)) {
            throw new IsNotACollectionException("The given class '$class' is not a collection.");
        }
    }
    
    public function loadCollection(string $class, int $id)
    {
        $this->checkCollection($class);
        
        $object = new $class();
        $object->load($id);
        
        return $object;
    }
    
    public function deleteCollection(string $class, int $id)
    {
        
    }
    
    /**
     * To find collections via their name they should be registered
     * @param string $collection
     */
    public function registerCollection(string $collection)
    {
        $this->checkCollection($collection);
        
        $this->collections[$collection::getInfo('name')] = $collection;    
    }
    
    /**
     * Searches for a collection either via its name or via its namespace
     * @param string $name
     * @throws IsNotACollectionException
     * @return string The namespace of the collection
     */
    public function searchCollection(string $name)
    {
        if (isset($this->collections[$name])) {
            return $this->collections[$name];
        }
        if (is_a($name, Collection::class, true)) {
            return $name;
        }
        throw new IsNotACollectionException("The given class '$name' is not the name of a collection.");
    }
    
    public function getRegisteredCollections()
    {
        return $this->collections;
    }
}
