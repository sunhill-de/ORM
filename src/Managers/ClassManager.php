<?php

/**
 * @file ClassManager.php
 * Provides the ClassManager object for accessing information about the orm classes
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-23
 * Localization: not necessary
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: 98,8% (2023-03-23)
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\ORMException;
use Illuminate\Support\Facades\Lang;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Utils\ObjectMigrator;
use Sunhill\ORM\Storage\StorageBase;
use Doctrine\Common\Lexer\Token;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Query\BasicQuery;
use Sunhill\ORM\Managers\Exceptions\ClassNotAccessibleException;
use Sunhill\ORM\Managers\Exceptions\ClassNotORMException;
use Sunhill\ORM\Managers\Exceptions\ClassNameForbiddenException;

 /**
  * Wrapper class for handling of objectclasses. It provides some public static methods to get informations about
  * the installed objectclasses. At this time there is no kind of registration of new objectclasses, they are just put
  * in one (or perhaps more) directory. Therefore the warpper needs to access this (these) directory and read 
  * out the single objectclasses. 
  * The problem is that objectclasses a called by namespace and autoloader and there is no sufficient method to 
  * get the installed objectclasses at the momement, so we have to read out the specific directories.
  * Definition of objectclass:
  * A descendand of Sunhill\ORM\Objects\ORMObject which represents a storable dataobject
  * @author lokal
  * Test: Unit/Managers/ManagerClassesTest.php
  */
class ClassManager 
{
 
    const FORBIDDEN_NAMES = ['object','class','integer','string','float','boolean','tag'];
    const FORBIDDEN_BEGINNINGS = ['attr_'];
    
    /**
     * Stores the information about the classes
     * @var array|null
     */
    protected $classes=[];
    
    private $flushing = false;
    
// ********************************** Register class ******************************************

    public function __construct() 
    {
         $this->flushClasses();
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
        
        $this->getClassInformationEntries($result,$classname);
        $this->getClassParentEntry($result,$classname);
        $this->getClassPropertyEntries($result,$classname);
        
        return $result;
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
     
     /**
      * Checks if the given classpath is a descendant of ORMObject
      * @param string $classpath
      * @throws ClassNotORMException
      */
     protected function checkClassType(string $classpath)
     {
         if (!is_a($classpath, ORMObject::class,true)) {
             throw new ClassNotORMException("The class '$classpath' is not a descendant of ORMObject");
         }
     }

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
     
    /**
     * Every single class that should be accessible via the class manager should be added through this method. 
     * It is possible to use an ORMObject without registering even store it but all references to other
     * classes (like PropertyObject) is performed via the classname. Therefore yout should register it.
     *  
     * @param $classname string The fully qualified name of the class to register
     * @return bool true if successful false if not
     * @throws ClassNotAccessibleException::class when the class is not found
     * @throws ClassNameForbiddenException::class when the class name is invalid
     * @throws ClassNotORMException::class when the class is not a descendant of ORMObject
     * 
     * Test: testRegisterClass*
     */
    public function registerClass(string $classname, bool $ignore_duplicate = false): bool 
    {
        $this->checkClassExistance($classname);
        $this->checkClassType($classname);
        $this->checkClassName($classname);
        
        $information = $this->buildClassInformation($classname);
        if (isset($this->classes[$information->name])) {
            if ($ignore_duplicate) {
                return $this->classes[$information->name]; 
            } else {
                throw new ORMException("The class '$classname' is already registered.");
            } 
        }
        $this->classes[$information->name] = $information;
        return true;
    }
    
    /**
     * Clears the class information array and insert the ORMObject by hand
     * @todo: buildClassInformation builds an infinite loop. So this is done by hand
     * Test: testFlushClasses
     */
    public function flushClasses(): void 
    {
        $this->classes = [
            'object'=>
            $this->buildClassInformation(ORMObject::class)
        ];
    }
    
// *************************** General class informations ===============================    
    /**
     * Returns the number of registered classes
     * 
     * @return int the number of registered Classes
     * 
     * Test: testNumberOfClasses
     */
    public function getClassCount(): int 
    {

        return count($this->classes);       
    }
    
    /**
     * Returns a treversable associative array of all registered classes
     * 
     * @return array the information of all registered classes
     * 
     * Test: testGetAllClasses
     */
    public function getAllClasses(): array 
    {
        
        return $this->classes;        
    }
    
    /**
     * Returns an array with the root ORMObject. Each entry is an array with the name of the
     * class as key and its children as another array.
     * Example:
     * ['object'=>['parent_object'=>['child1'=>[],'child2'=[]],'another_parent'=>[]]
     * 
     * Test: testGetClassTree
     */
    public function getClassTree(string $class = 'object') 
    {
        return [$class=>$this->getChildrenOfClass($class)];
    }
    
    // *************************** Informations about a specific class **************************    
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
        if (is_object($needle) && is_a($needle, ORMObject::class)) {
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
        foreach ($this->classes as $name => $info) {
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
        if (isset($this->classes[$needle]) || ($needle === 'object')) {
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
        foreach ($this->classes as $class_name => $info) {
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
        throw new ORMException("Unknown type for getClassName() ");
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
            throw new ORMException("Null was passed to checkClass");
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
            return $this->classes[$name];
        } else {
            if ($this->classes[$name]->class::hasInfo($field)) {
                // Pass it through getField to get translation (if there is any)
                return $this->classes[$name]->class::getInfo($field);                
            } else {
                return $this->classes[$name]->$field;
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
     * Returns the inheritance of the given class.
     * 
     * @param $class The class to get the parent of
     * @param bool $include_self
     * 
     * Test: testGetInheritance
     */
    public function getInheritanceOfClass($class,bool $include_self = false) 
    {
        $class = $this->checkClass($class);

        if ($include_self) {
            $result = [$class];
        } else {
            $result = [];
        }
        
        do {
            $class = $this->getParentOfClass($class);
            $result[] = $class;
        } while ($class !== 'object');
        
        return $result;
    }
       
    /**
     * Return an associative array of the children of the passed class. The array is in the form
     *  name_of_child=>[list_of_children_of_this_child]
     *  
     * @param string $class Name of the class to which all children should be searched. Default=object
     * @param int $level search children only to this depth. -1 means search all children. Default=-1
     * 
     * Test: testGetChildrenOfClass
     */
    public function getChildrenOfClass($class='object',int $level=-1) : array 
    {
        $class = $this->checkClass($class);
        
        $result = [];
        if (!$level) { // We reached top level
            return $result;
        }
        foreach ($this->classes as $class_name => $info) {
            if ($info->parent === $class) {
                $result[$class_name] = $this->getChildrenOfClass($class_name,$level-1);
            }
        }
        return $result;
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
    
    /**
     * Returns an array of all used tables of this class
     * 
     * @param string $class
     * 
     * Test: testGetUsedTablesOfClass
     */
    public function getUsedTablesOfClass(string $class)
    {
        $inheritance = $this->getInheritanceOfClass($class, true);
        
        $result = [];
        
        foreach ($inheritance as $ancestor) {
            $result[] = $this->getTableOfClass($ancestor);    
        }
        
        return $result;
    }
    
    /**
     * Creates an instance of the passes class
     * 
     * @param string $class is either the namespace or the class name
     * @return ORMObject The created instance of $class
     * 
     * Test: testCreateObject
     */
    public function createObject($class) 
    {
        $classspace = $this->getNamespaceOfClass($class);

        return new $classspace();        
    }
       
    /**
     * The reimplementation of is_a() that works with class names too
     * 
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     * 
     * Test: testIsA
     */
    public function isA($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($class);
        
        return is_a($test,$namespace, true);
    }
    
    /**
     * Returns true is $test is exactly a $class and not of its children
     * 
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     * 
     * Test: isAClass
     */
    public function isAClass($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        return is_a($test,$namespace, true) && !is_subclass_of($test,$namespace);
    }
   
    /**
     * Naming convention compatible method
     * The reimplementation of is_subclass_of() that works with class names too
     * 
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     * 
     * Test: isSubclassOf
     */
    public function isSubclassOf($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        $test_space = $this->getNamespaceOfClass($this->checkClass($this->searchClass($test)));
        return is_subclass_of($test_space,$namespace);        
    }
    
    /**
     * Creates the necessary tables for this class and checks if the fields are up to date
     */
    public function migrateClass(string $class_name) 
    {
        $class_namespace = $this->getNamespaceOfClass($class_name);
        $class_namespace::migrate();
    }
    
    public function migrateClasses()
    {
        $classes = $this->getAllClasses();
        if (!empty($classes)) {
            foreach($classes as $name => $infos) {
                $this->migrateClass($name);
            }
        }        
    }
    
    public function query(): BasicQuery
    {
        return new ClassQuery();
    }
}
