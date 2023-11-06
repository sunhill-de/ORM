<?php

namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Managers\Exceptions\ClassNotAccessibleException;
use Sunhill\ORM\Managers\Exceptions\ClassNameForbiddenException;

abstract class PropertiesCollectionManager extends RegistableManagerBase
{
    
    const FORBIDDEN_NAMES = ['object','class','integer','string','float','boolean','tag'];
    const FORBIDDEN_BEGINNINGS = ['attr_'];
    
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
    protected function buildClassInformation(string $classname): \StdClass
    {
        $result = new \StdClass();
        $result->class = $classname;
        
        $this->getClassInformationEntries($result,$classname);
        $this->getClassPropertyEntries($result,$classname);
        $this->buildAdditionalInformation($result,$classname);
        
        return $result;
    }
    
    protected function buildAdditionalInformation(\StdClass $result,string $class): void
    {
        // Do nothing by default
    }
    
    /**
     * Checks if the given classpath even exists
     * @param string $classpath
     * @throws ClassNotAccessibleException
     * @return boolean
     */
    protected function checkClassExistance(string $classpath)
    {
        if (!class_exists($classpath)) {
            throw new ClassNotAccessibleException("The class '$classpath' is not accessible.");
            return false;
        }
    }
    
    abstract protected function checkClassType(string $classpath);
    
    /**
     * Checks if the given classname is allowed
     * @param string $classpath
     * @return bool
     */
    protected function isClassNameForbidden(string $classname): bool
    {
        return in_array($classname, ClassManager::FORBIDDEN_NAMES);
    }
    
    /**
     * Checks if the classname begins with a forbidden string
     * @param string $classpath
     * @return bool
     */
    protected function isClassBeginningForbidden(string $classname): bool
    {
        foreach (ClassManager::FORBIDDEN_BEGINNINGS as $beginning) {
            if (substr($classname,0,strlen($beginning)) == $beginning) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Checks if the classname is allowed
     * @param string $classpath
     * @throws ClassNameForbiddenException
     */
    protected function checkClassName(string $classpath)
    {
        if ($this->isClassNameForbidden(strtolower($classpath::getInfo('name'))) || $this->isClassBeginningForbidden(strtolower($classpath::getInfo('name')))) {
            throw new ClassNameForbiddenException("The classname '".$classpath::getInfo('name')."' is no allowed.");
        }
    }
    
    protected function checkValidity($item)
    {
        $this->checkClassExistance($item);
        $this->checkClassType($item);
        $this->checkClassName($item);
    }
    
    protected function getItemInformation($item)
    {
        return $this->buildClassInformation($item);
    }
    
    protected function getItemKey($item): string
    {
        return $item::getInfo('name');
    }
    
    
}