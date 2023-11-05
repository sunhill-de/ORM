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
        return $class;
    }
    
    public function loadCollection(string $class, int $id)
    {
        $class = $this->checkCollection($class);
                
        $object = new $class();
        $object->load($id);
        
        return $object;
    }
    
    public function collectionExists(string $class, int $id)
    {
        $class = $this->checkCollection($class);
        
        return $class::IDExists($id);
    }
    
    public function deleteCollection(string $class, int $id)
    {
        
    }
    
    /**
     * Get the class informations and adds them to $result
     *
     * @param $result The array to store the information to
     * @param $class The full namespace of the class (not the class name!)
     *
     * Test: testGetClassInformationEntries
     */
    private function getClassInformationEntries(\StdClass $result,string $class): void
    {
        foreach ($class::getAllInfos() as $key => $value) {
            if ($value->translatable) {
                $result->$key = __($value->value);
            } else {
                $result->$key = $value->value;
            }
        }
    }
    
    /**
     * Returns the parent entry of this class
     *
     * @param array $result The array to store the information to
     * @param string $class The full namespace of the class (not the class name!)
     *
     * Test: testGetClassParentEntry
     */
    private function getClassParentEntry(\StdClass $result,string $class): void
    {
        $parent = get_parent_class($class);
        if ($class !== ORMObject::class) {
            $result->parent = $parent::getInfo('name');
        } else {
            $result->parent = '';
        }
    }
    
    /**
     * Return all properties of the given class
     *
     * @param string $class The full namespace of the class (not the class name!)
     * @return array The properties of the given class
     *
     * Test: testGetClassProperties
     */
    private function getClassProperties(string $class): array
    {
        $properties = $class::getAllPropertyDefinitions();
        $result = [];
        foreach ($properties as $name => $descriptor) {
            if ($name !== 'tags') {
                $result[$name] = $descriptor;
            }
        }
        return $result;
    }
    
    /**
     * Inserts the class properties in the result array
     *
     * @param array $result The array to store the information to
     * @param string $class The full namespace of the class (not the class name!)
     *
     * Test: testGetClassPropertyEntries
     */
    private function getClassPropertyEntries(\StdClass &$result,string $class): void
    {
        $result->properties = [];
        $properties = $this->getClassProperties($class);
        foreach ($properties as $property) {
            $result->properties[$property->getName()] = [];
            $features = $property->getAttributes();
            foreach ($features as $feat_key => $feat_value) {
                $result->properties[$property->getName()][$feat_key] = $feat_value;
            }
        }
    }
    /**
     * Collects all data about this class to store it in the classes array
     *
     * @param $classname string The name of the class to collect values from
     * @return array associative array with informations about this class
     *
     * test: testBuildClassInformation
     */
    private function buildClassInformation(string $classname): \StdClass
    {
        $result = new \StdClass();
        $result->class = $classname;
        
        $this->getCollectionInformationEntries($result,$classname);
        $this->getCollectionPropertyEntries($result,$classname);
        
        return $result;
    }
    
    /**
     * To find collections via their name they should be registered
     * @param string $collection
     */
    public function registerCollection(string $collection)
    {
        $this->checkCollection($collection);
        
        $information = $this->buildCollectionInformation($classname);
        
        $this->collections[$collection::getInfo('name')] = $information;    
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
    
    public function migrateCollections()
    {
        foreach ($this->collections as $name => $namespace) {
            $namespace::migrate();
        }
    }
    
    public function query(): BasicQuery
    {
        return new CollectionQuery();
    }
}
