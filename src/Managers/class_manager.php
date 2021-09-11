<?php

/**
 * @file class_manager.php
 * Provides the class_manager object for accessing information about the orm classes
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2020-09-13
 * Localization: unknown
 * Documentation: unknown
 * Tests: tests/Unit/Managers/ManagerClassesTest.php
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\ORMException;
use Illuminate\Support\Facades\Lang;
use Sunhill\Basic\Utils\descriptor;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Objects\Utils\object_migrator;

 /**
  * Wrapper class for handling of objectclasses. It provides some public static methods to get informations about
  * the installed objectclasses. At this time there is no kind of registration of new objectclasses, they are just put
  * in one (or perhaps more) directory. Therefore the warpper needs to access this (these) directory and read 
  * out the single objectclasses. 
  * The problem is that objectclasses a called by namespace and autoloader and there is no sufficient method to 
  * get the installed objectclasses at the momement, so we have to read out the specific directories.
  * Definition of objectclass:
  * A descendand of Sunhill\ORM\Objects\oo_object which represents a storable dataobject
  * @author lokal
  *
  */
class class_manager {
 
    private static $translatable = [/*'name_s','name_p','description'*/];
        
    /**
     * Stores the information about the classes
     * @var array|null
     */
    private $classes=[];
// ********************************** Register class ******************************************

    public function __construct() {
        $this->flushClasses();    
    }
    
    /**
     * Get the fully qualified class name and adds it to $result
     */
    private function getClassEntry(array &$result,string $class) : void {
        $result['class'] = $class;
    }
    
    /**
     * Get the class informations and adds them to $result
     * @
     */
    private function getClassInformationEntries(array &$result,string $class) : void {
        foreach ($class::$object_infos as $key => $value) {
            $result[$key] = $value;
        }
    }
    
    private function getClassParentEntry(array &$result,string $class) : void {
        $parent = get_parent_class($class);
        if ($class !== 'object') { // Strange infinite loop bug
            $result['parent'] = $parent::$object_infos['name'];
        }
    }
    
    private function getClassProperties(string $class) {
        $properties = $class::static_get_properties_with_feature();
        $result = [];
        foreach ($properties as $name => $descriptor) {
            if ($name !== 'tags') {
                $result[$name] = $descriptor;
            }
        }
        return $result;
    }
    
    private function getClassPropertyEntries(array &$result,string $class) : void {
        $result['properties'] = [];
        $properties = $this->getClassProperties($class);
        foreach ($properties as $property) {
            $result['properties'][$property->get_name()] = [];
            $features = $property->get_static_attributes();
            foreach ($features as $feat_key => $feat_value) {
                $result['properties'][$property->get_name()][$feat_key] = $feat_value;
            }            
        }    
   }
    
    /**
     * Collects all data about this class to store it in the classes array
     * @param $classname string The name of the class to collect values from
     * @return array associative array with informations about this class
     */
    private function buildClassInformation(string $classname) : array {
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
     * @param $classname string The fully qualified name of the class to register
     * @return bool true if successful false if not
     */
    public function registerClass(string $classname) : bool {
        if (!class_exists($classname)) {
            throw new ORMException("The class '$classname' is not accessible.");
            return false;
        } 
        if (isset($this->classes[$classname])) {
            throw new ORMException("The class '$classname' is already registered.");
        }
        $information = $this->buildClassInformation($classname);
        $this->classes[$information['name']] = $information;
        return true;
    }
    
    /**
     * Clears the class information array
     */
    public function flushClasses() : void {
        $this->classes = ['object'=>['table'=>'objects','class'=>oo_object::class,'parent'=>null,'name'=>'object']];
    }
    
// *************************** General class informations ===============================    
    /**
     * Alias for getClassCount()
     * @deprecated use getClassCount()
     */
    public function get_class_count() {
        return $this->getClassCount();
    }

    /**
     * Returns the number of registered classes
     */
    public function getClassCount() : int {

        return count($this->classes);       
    }
    
    /**
     * Alias for getAllClasses
     * @deprecated use getAllClasses
     * @return unknown
     */
    public function get_all_classes() {
        return $this->getAllClasses();
    }
    
    /**
     * Returns a treversable associative array of all registered classes
     * @return unknown
     */
    public function getAllClasses() : array {
        
        return $this->classes;        
    }
    
    /**
     * Alias for getClassTree() 
     * @deprecated use getClassTree 
     */
    public function get_class_tree(string $class = 'object') {
        return $this->getClassTree($class);
    }
    
    /**
     * Returns an array with the root oo_object. Each entry is an array with the name of the
     * class as key and its children as another array.
     * Example:
     * ['object'=>['parent_object'=>['child1'=>[],'child2'=[]],'another_parent'=>[]]
     */
    public function getClassTree(string $class = 'object') {
        return [$class=>$this->getChildrenOfClass($class)];
    }
    
    // *************************** Informations about a specific class **************************    
    private function notExists($test) {
        if ($this->search_class($test)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Alias for normalizeNamespace
     * @param string $namespace
     * @return string
     * @deprecated use normalizeNamespace()
     */
    public function normalize_namespace(string $namespace) : string {
        return $this->normalizeNamespace($namespace);
    }

    /**
     * Normalizes the passed namespace (removes heading \ and double backslashes)
     * @param string $namespace
     * @return string
     */
    public function normalizeNamespace(string $namespace) : string {
        $namespace = str_replace("\\\\","\\",$namespace);
        if (strpos($namespace,'\\') == 0) {
            return substr($namespace,1);
        } else {
            return $namespace;
        }
    }
    
    /**
     * This method returns the name of the class or null
     * Alias for searchClass
     * @deprecated use searchClass
     * If $needle is a string with backslashes it searches the correspending class name
     * If $needle is a string without backslahes it just returns the name
     * if $needle is an object it gets the namespace of this object and searches it
     * @param string $needle
     */
    public function search_class($needle) {
        return $this->searchClass($needle);        
    }
    
    /**
     * This method returns the name of the class or null
     * If $needle is a string with backslashes it searches the correspending class name
     * If $needle is a string without backslahes it just returns the name
     * if $needle is an object it gets the namespace of this object and searches it
     * @param string $needle
     */
    public function searchClass($needle) {
        
        if (is_object($needle)) {
            $needle = get_class($needle);
        }
        if (strpos($needle,'\\') !== false) {
            $needle = $this->normalize_namespace($needle);
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
     * @deprecated use getClassName
     */
    public function get_class_name($test) {
        return $this->getClassName($test);
    }
    
    /**
     * Returns the (internal) name of the class. It doesn't matter how the class is passed (name, namespace, object or index)
     * @param unknown $test Could be either a string, an object or an integer
     */
    public function getClassName($test) {
        if (is_int($test)) {
            return $this->getClassnameWithIndex($test);
        } else if (is_string($test)) {
            if (strpos($test,'\\') !== false) {
                // We have a namespace
                return $test::$object_infos['name'];
            } else {
                return $test;
            }
        } else if (is_object($test)) {
            if (is_a($test,oo_object::class)) {
                return $test::$object_infos['name'];
            } else {
                throw new ORMException("Invalid object passed to get_class: ".get_class($test));
            }
        } else {
            throw new ORMException("Unknown type for get_class_name()");
        }
    }
    
    /**
     * Returns the class with the number $index
     * @param int $index The number of the wanted class
     * @retval string
     */
    private function getClassnameWithIndex(int $index) {
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
    private function checkClass($test) {
        if (is_null($test)) {
            throw new ORMException("Null was passed to checkClass");
        }
        $name = $this->getClassName($test);
        if (!isset($this->classes[$name]) && ($name !== 'object')) {
            throw new ORMException("The class '$name' doesn't exists.");
        }
        return $name;
    }
    

    private function translate(string $class,string $item) {
        return Lang::get('ORM:testfiles.'.$class.'_'.$item);
    }
    
    /**
     * Searches for the class named '$name'
     * @deprecated use getClass()
     * @param string $name
     * @param unknown $field
     * @throws ORMException
     * @return unknown
     */
    public function get_class($test,$field=null) {
        return $this->getClass($test,$field);
    }
    
    /**
     * Searches for the class named '$name'
     * @param string $name
     * @param unknown $field
     * @throws ORMException
     * @return unknown
     */
    public function getClass($test,$field=null) {        
        $name = $this->getClassName($test);
        $this->checkClass($name);
        $class = $this->classes[$name];
        if (is_null($field)) {
            return $class;
        } else {
            if (in_array($field,static::$translatable)) {
                return $this->translate($name,$field);
            } else if (array_key_exists($field,$class)) {
                return $class[$field];
            } else {
                throw new ORMException("The class '$name' doesn't export '$field'.");
            }
        }
    }
    
    /**
     * Return the table of class '$name'. Alias for get_class($name,'table')
     * Alias for getTableOfClass()
     * @deprecated use getTableOfClass()
     * @param string $name
     * @return unknown
     */
    public function get_table_of_class(string $name) {
        return $this->getTableOfClass($name);
    }
    
    /**
     * Return the table of class '$name'. Alias for get_class($name,'table')
     * @param string $name
     * @return unknown
     */
    public function getTableOfClass(string $name) {
        $name = $this->checkClass($this->searchClass($name));
        return $this->get_class($name,'table');
    }
    
    /**
     * Return the parent of class '$name'. Alias for get_class($name,'parent')
     * @deprecated use getParentOfClass
     * @param string $name
     * @return unknown
     */
    public function get_parent_of_class(string $name) {
        return $this->getParentOfClass($name);
    }

    /**
     * Return the parent of class '$name'. Alias for get_class($name,'parent')
     * @param string $name
     * @return unknown
     */
    public function getParentOfClass(string $name) {
        $name = $this->checkClass($this->searchClass($name));
        return $this->get_class($name,'parent');
    }
    
    /**
     * Returns the inheritance of the given class. 
     * @deprecated use getInheritanceOfClass
     * @param string $name
     * @param bool $include_self
     */
    public function get_inheritance_of_class(string $name,bool $include_self=false) {
        return $this->getInheritanceOfClass($name,$include_self);
    }

    /**
     * Returns the inheritance of the given class.
     * @param string $name
     * @param bool $include_self
     */
    public function getInheritanceOfClass(string $name,bool $include_self=false) {
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
    public function get_children_of_class(string $name='object',int $level=-1) : array {
        return $this->getChildrenOfClass($name,$level);
    }
    
    /**
     * Return an associative array of the children of the passed class. The array is in the form
     *  name_of_child=>[list_of_children_of_this_child]
     * @param string $name Name of the class to which all children should be searched. Default=object
     * @param int $level search children only to this depth. -1 means search all children. Default=-1
     */
    public function getChildrenOfClass(string $name='object',int $level=-1) : array {
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
     * @deprecated use getPropertiesOfClass
     * @param string $class The class to search for properties
     * @return descriptor of all properties
     */
    public function get_properties_of_class(string $class) {
        return $this->getPropertiesOfClass($class);
    }
    
    /**
     * Returns all properties of the given class
     * @param string $class The class to search for properties
     * @return descriptor of all properties
     */
    public function getPropertiesOfClass(string $class) {
        $name = $this->checkClass($this->searchClass($class));
        return $this->getClass($name,'properties');
    }
    
    /**
     * Return only the descriptor of a given property of a given class
     * @deprecated use getPropertyOfClass
     * @param string $class The class to search for the property
     * @param string $property The property to search for
     * @return descriptor of this property
     */
    public function get_property_of_class(string $class,string $property) {        
        return $this->getPropertyOfClass($class,$property);
    }
    
    /**
     * Return only the descriptor of a given property of a given class
     * @param string $class The class to search for the property
     * @param string $property The property to search for
     * @return descriptor of this property
     */
    public function getPropertyOfClass(string $class,string $property) {
        $class = $this->checkClass($this->searchClass($class));
        return $this->getPropertiesOfClass($class)[$property];
    }
    
    /**
     * Return the full qualified namespace name of the class 'name'. Alias for get_class($name,'class')
     * @deprecated use getNamespaceOfClass
     * @param string $name
     * @return unknown
     */
    public function get_namespace_of_class(string $name) {
        return $this->getNamespaceOfClass($name);
    }
    
    /**
     * Return the full qualified namespace name of the class 'name'. Alias for get_class($name,'class')
     * @param string $name
     * @return unknown
     */
    public function getNamespaceOfClass(string $name) {
        $name = $this->checkClass($this->searchClass($name));
        return $this->getClass($name,'class');
    }
    
    /**
     * Creates an instance of the passes class
     * @deprecated use createObject
     * @param string $class is either the namespace or the class name 
     * @return oo_object The created instance of $class
     */
    public function create_object(string $class) {
        return $this->createObject($class);
    }
    
    /**
     * Creates an instance of the passes class
     * @param string $class is either the namespace or the class name
     * @return oo_object The created instance of $class
     */
    public function createObject(string $class) {
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
    public function is_a($test,$class) {
        return $this->isA($test,$class);
    }
    
    /**
     * The reimplementation of is_a() that works with class names too
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function isA($test,$class) {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        return is_a($test,$namespace);
    }
    
    /**
     * Returns true is $test is exactly a $class and not of its children
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function is_a_class($test,$class) {
        return $this->isAClass($test,$class);
    }
    
    /**
     * Returns true is $test is exactly a $class and not of its children
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function isAClass($test,$class) {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        return is_a($test,$namespace) && !is_subclass_of($test,$namespace);
    }

    /**
     * The reimplementation of is_subclass_of() that works with class names too
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function is_subclass_of($test,$class) {
        return $this->isSubclassOf($test,$class);
    }

    
    /**
     * Naming convention compatible method
     * The reimplementation of is_subclass_of() that works with class names too
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function isSubclassOf($test,$class) {
        $namespace = $this->getNamespaceOfClass($this->checkClass($this->searchClass($class)));
        $test_space = $this->getNamespaceOfClass($this->checkClass($this->searchClass($test)));
        return is_subclass_of($test_space,$namespace);        
    }
    
    /**
     * Alias for @see migrateClass()
     */
    public function migrate_class(string $class_name) {
        return $this->migrateClass($class_name);
    }
    
    /**
     * Creates the necessary tables for this class and checks if the fields are up to date
     */
    public function migrateClass(string $class_name) : void {
        $class_name = $this->checkClass($this->search_class($class_name));
        $migrator = new object_migrator();
        $migrator->migrate($class_name);
    }
}
