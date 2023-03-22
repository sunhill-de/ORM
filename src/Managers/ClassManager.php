<?php

/**
 * @file ClassManager.php
 * Provides the ClassManager object for accessing information about the orm classes
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-10-05
 * Localization: unknown
 * Documentation: unknown
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\ORMException;
use Illuminate\Support\Facades\Lang;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Utils\ObjectMigrator;
use Doctrine\Common\Lexer\Token;
use Sunhill\ORM\Facades\Classes;

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
     * Get the fully qualified class name and adds it to $result
     * 
     * @param $result The array to store the information to
     * @param $class The full namespace of the class (not the class name!)
     * 
     * Test: testGetClassEntry
     */
    private function getClassEntry(array &$result,string $class): void 
    {
        $result['class'] = $class;
    }
    
    /**
     * Get the class informations and adds them to $result
     * 
     * @param $result The array to store the information to
     * @param $class The full namespace of the class (not the class name!)
     * 
     * Test: testGetClassInformationEntries
     */
    private function getClassInformationEntries(array &$result,string $class): void 
    {
        foreach ($class::getAllInfos() as $key => $value) {
            $result[$key] = $class::getInfo($key); 
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
    private function getClassParentEntry(array &$result,string $class): void 
    {
        $parent = get_parent_class($class);
        if ($class !== 'object') { // Strange infinite loop bug
            $result['parent'] = $parent::getInfo('name');
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
        $properties = $class::staticGetPropertiesWithFeature();
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
    private function getClassPropertyEntries(array &$result,string $class): void 
    {
        $result['properties'] = [];
        $properties = $this->getClassProperties($class);
        foreach ($properties as $property) {
            $result['properties'][$property->getName()] = [];
            $features = $property->getStaticAttributes();
            foreach ($features as $feat_key => $feat_value) {
                $result['properties'][$property->getName()][$feat_key] = $feat_value;
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
    private function buildClassInformation(string $classname): array 
    {
        $result = [];
        
        $this->getClassEntry($result,$classname);
        $this->getClassInformationEntries($result,$classname);
        $this->getClassParentEntry($result,$classname);
        $this->getClassPropertyEntries($result,$classname);
        
        return $result;
     }
    
    /**
     * Every single class that should be accessible via the class manager should be added through this method. 
     * In opposite to the above cache file (wich is deprecated then) this allowes testing to be easier.
     *  
     * @param $classname string The fully qualified name of the class to register
     * @return bool true if successful false if not
     * 
     * Test: testRegisterClass*
     */
    public function registerClass(string $classname): bool 
    {
        if (!class_exists($classname)) {
            throw new ORMException("The class '$classname' is not accessible.");
            return false;
        } 
        $information = $this->buildClassInformation($classname);
        if (isset($this->classes[$information['name']])) {
            throw new ORMException("The class '$classname' is already registered.");
        }
        $this->classes[$information['name']] = $information;
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
            'object'=>[
                'class'=>ORMObject::class,
                'name'=>'object',
                'table'=>'objects',
                'name_s'=>'object',
                'name_p'=>'objects',
                'description'=>'The base class for any storable object',
                'parent'=>'',
                'properties'=>[
                    'created_at'=>[
                        'class'=>'object',
                        'defaults_null'=>false,
                        'features'=>['object','complex'],
                        'name'=>'created_at',
                        'read_only'=>false,
                        'searchable'=>false,
                        'type'=>'timestamp'                        
                    ],
                    'updated_at'=>[
                        'class'=>'object',
                        'defaults_null'=>false,
                        'features'=>['object','complex'],
                        'name'=>'updated_at',
                        'read_only'=>false,
                        'searchable'=>false,
                        'type'=>'timestamp'
                    ],
                    'uuid'=>[
                        'class'=>'object',
                        'defaults_null'=>false,
                        'features'=>['object','simple'],
                        'name'=>'uuid',
                        'read_only'=>false,
                        'searchable'=>true,
                        'type'=>'varchar'
                    ],
                ]                
            ]
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
     * @param string $namespace
     * @return string
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
     * This method returns the name of the class or null
     * If $needle is a string with backslashes it searches the correspending class name
     * If $needle is a string without backslahes it just returns the name
     * if $needle is an object it gets the namespace of this object and searches it
     * @param string $needle
     */
    public function searchClass($needle) 
    {
        
        if (is_object($needle)) {        
            
            $needle = get_class($needle);
        }
        if (strpos($needle,'\\') !== false) {
            $needle = $this->normalizeNamespace($needle);
            foreach ($this->classes as $name => $info) {
                if ($info['class'] === $needle) {
                    return $info['name'];
                }
            }
            return null;
        }  else {
            if (isset($this->classes[$needle]) || ($needle === 'object')) {
                return $needle;
            } else {
                return null;
            }
        }
    }
    
    /**
     * Returns the (internal) name of the class. It doesn't matter how the class is passed (name, namespace, object or index)
     * @param unknown $test Could be either a string, an object or an integer
     */
    public function getClassName($test) 
    {
        if (is_int($test)) {
            return $this->getClassnameWithIndex($test);
        } else if (is_string($test)) {
            if (strpos($test,'\\') !== false) {
                // We have a namespace
                return $test::getInfo('name');
            } else {
                return $test;
            }
        } else if (is_object($test)) {
            if (is_a($test,ORMObject::class)) {
                return $test::getInfo('name');
            } else {
                throw new ORMException("Invalid object passed to get_class: ".get_class($test));
            }
        } else {
            throw new ORMException("Unknown type for getClassName()");
        }
    }
    
    /**
     * Returns the class with the number $index
     * @param int $index The number of the wanted class
     * @retval string
     */
    private function getClassnameWithIndex(int $index) 
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
     * Tests if this class is in the class cache
     * @param unknown $test The class to test
     */
    private function checkClass($test) 
    {
        if (is_null($test)) {
            throw new ORMException("Null was passed to checkClass");
        }
        $name = $this->getClassName($test);
        if (!isset($this->classes[$name]) && ($name !== 'object')) {
            throw new ORMException("The class '$name' doesn't exists.");
        }
        return $name;
    }
    

    private function translate(string $class,string $item) 
    {
        return Lang::get('ORM:testfiles.'.$class.'_'.$item);
    }
       
    /**
     * Searches for the class named '$name'
     * @param string $name
     * @param unknown $field
     * @throws ORMException
     * @return unknown
     */
    public function getClass($test,$field=null) 
    {        
        $name = $this->getClassName($test);
        $this->checkClass($name);
        $class = $this->classes[$name];
        if (is_null($field)) {
            return $class;
        } else {
            if (array_key_exists($field,$class)) {
                return $class[$field];
            } else {
                throw new ORMException("The class '$name' doesn't export '$field'.");
            }
        }
    }
       
    /**
     * Return the table of class '$name'. Alias for getClass($name,'table')
     * @param string $name
     * @return unknown
     */
    public function getTableOfClass(string $name) 
    {
        $name = $this->checkClass($this->searchClass($name));
        return $this->getClass($name,'table');
    }
    
    /**
     * Return the parent of class '$name'. Alias for getClass($name,'parent')
     * @param string $name
     * @return unknown
     */
    public function getParentOfClass(string $name) 
    {
        $name = $this->checkClass($this->searchClass($name));
        return $this->getClass($name,'parent');
    }
    
    /**
     * Returns the inheritance of the given class.
     * @param string $name
     * @param bool $include_self
     */
    public function getInheritanceOfClass(string $name,bool $include_self = false) 
    {
        $name = $this->checkClass($this->searchClass($name));
        if ($include_self) {
            $result = [$name];
        } else {
            $result = [];
        }
        do {
            $name = $this->getParentOfClass($name);
            $result[] = $name;
        } while ($name !== 'object');
        return $result;
    }
       
    /**
     * Return an associative array of the children of the passed class. The array is in the form
     *  name_of_child=>[list_of_children_of_this_child]
     * @param string $name Name of the class to which all children should be searched. Default=object
     * @param int $level search children only to this depth. -1 means search all children. Default=-1
     */
    public function getChildrenOfClass(string $name='object',int $level=-1) : array 
    {
        $name = $this->checkClass($this->searchClass($name));
        
        $result = [];
        if (!$level) { // We reached top level
            return $result;
        }
        foreach ($this->classes as $class_name => $info) {
            if ($info['parent'] === $name) {
                $result[$class_name] = $this->getChildrenOfClass($class_name,$level-1);
            }
        }
        return $result;
    }
        
    /**
     * Returns all properties of the given class
     * @param string $class The class to search for properties
     * @return Descriptor of all properties
     */
    public function getPropertiesOfClass(string $class) 
    {
        $name = $this->checkClass($this->searchClass($class));
        return $this->getClass($name,'properties');
    }
    
    /**
     * Return only the Descriptor of a given property of a given class
     * @param string $class The class to search for the property
     * @param string $property The property to search for
     * @return Descriptor of this property
     */
    public function getPropertyOfClass(string $class,string $property) 
    {
        $class = $this->checkClass($this->searchClass($class));
        return $this->getPropertiesOfClass($class)[$property];
    }
       
    /**
     * Return the full qualified namespace name of the class 'name'. Alias for getClass($name,'class')
     * @param string $name
     * @return unknown
     */
    public function getNamespaceOfClass(string $name) 
    {
        $name = $this->checkClass($this->searchClass($name));
        return $this->getClass($name,'class');
    }
    
    /**
     * Returns an array of all used tables of this class
     * @param string $name
     */
    public function getUsedTablesOfClass(string $name)
    {
        $inheritance = $this->getInheritanceOfClass($name, true);
        
        $result = [];
        
        foreach ($inheritance as $ancestor) {
            $result[] = $this->getTableOfClass($ancestor);    
        }
        
        return $result;
    }
    
    /**
     * Creates an instance of the passes class
     * @param string $class is either the namespace or the class name
     * @return ORMObject The created instance of $class
     */
    public function createObject(string $class) 
    {
        $class = $this->checkClass($this->searchClass($class));
        $namespace = $this->getNamespaceOfClass($this->searchClass($class));
        $result = new $namespace();
        return $result;
    }
       
    /**
     * The reimplementation of is_a() that works with class names too
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function isA($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        return is_a($test,$namespace);
    }
    
    /**
     * Returns true is $test is exactly a $class and not of its children
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function isAClass($test,$class) 
    {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        return is_a($test,$namespace) && !is_subclass_of($test,$namespace);
    }
   
    /**
     * Naming convention compatible method
     * The reimplementation of is_subclass_of() that works with class names too
     * @param unknown $test
     * @param unknown $class
     * @return boolean
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
        $class_name = $this->checkClass($this->searchClass($class_name));
        $migrator = new ObjectMigrator();
        $migrator->migrate($class_name);
    }
}
