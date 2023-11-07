<?php

namespace Sunhill\ORM\Managers;

use Sunhill\ORM\Managers\Exceptions\ClassNotAccessibleException;
use Sunhill\ORM\Managers\Exceptions\ClassNameForbiddenException;
use Sunhill\ORM\Objects\PropertiesCollection;
use Sunhill\ORM\ORMException;

abstract class PropertiesCollectionManager extends RegistableManagerBase
{
    
    static protected $base_class = PropertiesCollection::class;
    
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
    
    /**
     * Normalizes the passed namespace (removes heading \ and double backslashes)
     *
     * @param string $namespace
     * @return string
     *
     * Test: testNormalizeNamespace
     */
    public function normalizeNamespace(string $namespace): string
    {
        $namespace = str_replace("\\\\","\\",$namespace);
        if (strpos($namespace,'\\') == 0) {
            return substr($namespace,1);
        } else {
            return $namespace;
        }
    }
   
    /**
     * Checks if $needle is a object. If yes try to find it via the namespace
     *
     * @param unknown $needle
     * @return NULL|string|void|boolean|boolean
     *
     * Test: testCheckForObject_pass, testCheckForObject_fail1, testCheckForObject_fail2
     */
    protected function checkForObject($needle)
    {
        if (is_object($needle) && is_a($needle, static::$base_class)) {
            return $this->searchClass($needle::class);
        }
        return false;
    }
    
    /**
     * Checks if $needle is a namespace of a registered class
     *
     * @param string $needle
     * @return boolean
     *
     * Test: testCheckForNamespace
     */
    protected function checkForNamespace(string $needle)
    {
        $needle = $this->normalizeNamespace($needle);
        foreach ($this->registered_items as $name => $info) {
            if ($info->class == $needle) {
                return $info->name;
            }
        }
        return false;
    }
    
    /**
     * Checks if $needle is a name of a registered class
     *
     * @param string $needle
     * @return \Sunhill\ORM\Managers\string|NULL
     *
     * Test: testCheckForClassname
     */
    protected function checkForClassname(string $needle)
    {
        if (isset($this->registered_items[$needle]) || ($needle === 'object')) {
            return $needle;
        }
        
        return null;
    }
    
    /**
     * Checks if $needle is a string and if yes, if it's a namespace or a classname
     *
     * @param unknown $needle
     * @return void|boolean|\Sunhill\ORM\Managers\string|NULL
     *
     * Test: testCheckForString
     */
    protected function checkForString($needle)
    {
        if (!is_string($needle)) {
            return;
        }
        if (strpos($needle,'\\') !== false) {
            return $this->checkForNamespace($needle);
        }
        return $this->checkForClassname($needle);
    }
    
    /**
     * If an int was passed, use this as an index into the classes array
     *
     * @param unknown $needle
     * @return void|array|NULL
     *
     * Test: testForInt
     */
    protected function checkForInt($needle)
    {
        if (!is_numeric($needle)) {
            return;
        }
        return $this->getClassnameWithIndex(intval($needle));
    }
    
    /**
     * Returns the class with the number $index
     * @param int $index The number of the wanted class
     * @retval string
     */
    protected function getClassnameWithIndex(int $index)
    {
        if ($index < 0) {
            throw new ORMException("Invalid Index '$index'");
        }
        $i=0;
        foreach ($this->registered_items as $class_name => $info) {
            if ($i==$index) {
                return $class_name;
            }
            $i++;
        }
        throw new ORMException("Invalid index '$index'");
    }
    
    /**
     * This method returns the name of the class or null
     * If $needle is a string with backslashes it searches the correspending class name
     * If $needle is a string without backslahes it just returns the name
     * if $needle is an object it gets the namespace of this object and searches it
     *
     * @param string $needle
     *
     * Test: testSearchClass
     */
    public function searchClass($needle): ?string
    {
        if ($result = $this->checkForObject($needle)) {
            return $result;
        }
        if ($result = $this->checkForString($needle)) {
            return $result;
        }
        if ($result = $this->checkForInt($needle)) {
            return $result;
        }
        return null;
    }
    
    /**
     * Returns the (internal) name of the class. It doesn't matter how the class is passed (name, namespace, object or index)
     * It calls searchClass but raises an exception when nothing is found
     * @param unknown $test Could be either a string, an object or an integer
     *
     * Test: testGetClassName
     */
    public function getClassName($test)
    {
        if ($result = $this->searchClass($test)) {
            return $result;
        }
        if (is_scalar($test)) {
            throw new ClassNotAccessibleException("'$test' is not accessible (not registered?)");
        } else {
            throw new ClassNotAccessibleException("Unknown type for getClassName() ");
            
        }
    }
    
    /**
     * Tests if this class is in the class cache
     *
     * @param unknown $test The class to test
     *
     * Test: testCheckClass
     */
    protected function checkClass($test)
    {
        if (is_null($test)) {
            throw new ClassNotAccessibleException("Null was passed to checkClass");
        }
        return $this->getClassName($test);
    }
    
    
    /**
     * Searches for the class named '$name', when $field is set it returns this field
     *
     * @param string $name
     * @param unknown $field
     * @throws ORMException
     * @return unknown
     *
     * Test: testGetClass
     */
    public function getClass($test,?string $field = null)
    {
        $name = $this->checkClass($this->getClassName($test));
        
        if (is_null($field)) {
            return $this->registered_items[$name];
        } else {
            if ($this->registered_items[$name]->class::hasInfo($field)) {
                // Pass it through getField to get translation (if there is any)
                return $this->registered_items[$name]->class::getInfo($field);
            } else {
                return $this->registered_items[$name]->$field;
            }
        }
    }
    
    /**
     * Return the table of class '$class'. Alias for getClass($class,'table')
     *
     * @param $class The class to get the table of
     * @return string The name of the database table
     *
     * Test: testClassTable
     */
    public function getTableOfClass($class): string
    {
        return $this->getClass($class,'table');
    }
    
    /**
     * Return the parent of class '$class'. Alias for getClass($class,'parent')
     *
     * @param $class The class to get the parent of
     * @return string The name of the parent of the given class
     *
     * Test: testClassParent
     */
    public function getParentOfClass($class): string
    {
        return $this->getClass($class,'parent');
    }
    
    /**
     * Returns all properties of the given class
     *
     * @param string $class The class to search for properties
     * @return Descriptor of all properties
     *
     * Test: testGetPropertiesOfClass
     */
    public function getPropertiesOfClass($class)
    {
        return $this->getClass($class,'properties');
    }
    
    /**
     * Return only the Descriptor of a given property of a given class
     *
     * @param string $class The class to search for the property
     * @param string $property The property to search for
     * @return Descriptor of this property
     *
     * Test: testGetPropertyOfClass
     */
    public function getPropertyOfClass($class,string $property)
    {
        return $this->getPropertiesOfClass($class)[$property];
    }
    
    /**
     * Return the full qualified namespace name of the class 'name'. Alias for getClass($class,'class')
     *
     * @param string $class
     * @return unknown
     *
     * Test: testGetNamespaceOfClass
     */
    public function getNamespaceOfClass($class)
    {
        $result = $this->getClass($class,'class');
        return $this->getClass($class,'class');
    }
    
    
}