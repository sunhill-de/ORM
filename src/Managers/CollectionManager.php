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
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Managers\Exceptions\ClassNotORMException;
use Sunhill\ORM\Managers\CollectionQuery\CollectionQuery;

class CollectionManager extends PropertiesCollectionManager
{
    
    static protected $base_class = Collection::class;
    
    /**
     * Checks if the given classpath is a descendant of ORMObject
     * @param string $classpath
     * @throws ClassNotORMException
     */
    protected function checkClassType(string $classpath)
    {
        if (!is_a($classpath, Collection::class,true)) {
            throw new ClassNotORMException("The class '$classpath' is not a descendant of Collection");
        }
    }
    
    public function loadCollection(string $class, int $id)
    {
        if (strpos($class, '\\') == false) {
            $class = $this->getNamespaceOfClass($this->searchClass($class));
        }
        $this->checkValidity($class);
        
        $object = new $class();
        $object->load($id);
        
        return $object;
    }
    
    public function collectionExists(string $class, int $id)
    {
        $this->checkValidity($class);
        
        return $class::IDExists($id);
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
        $this->register($collection);
    }
    
    /**
     * Searches for a collection either via its name or via its namespace
     * @param string $name
     * @throws IsNotACollectionException
     * @return string The namespace of the collection
     */
    public function searchCollection(string $name)
    {
        if (isset($this->registered_items[$name])) {
            return $this->registered_items[$name];
        }
        if (is_a($name, Collection::class, true)) {
            return $name;
        }
        throw new IsNotACollectionException("The given class '$name' is not the name of a collection.");
    }
    
    public function getAllCollections()
    {
        return $this->registered_items;
    }
    
    public function migrateCollections()
    {
        foreach ($this->registered_items as $name => $namespace) {
            ($namespace->class)::migrate();
        }
    }
    
    public function query(): BasicQuery
    {
        return new CollectionQuery();
    }
}
