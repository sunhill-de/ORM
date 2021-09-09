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
 * Tests: unknown
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
        
    private $object_dirs = [];
    /**
     * Stores the information about the classes
     * @var array|null
     */
    private $classes=null;

// ******************************** Cache-Management ***************************************    
    /**
     * Return the full path to the class cache file
     * @return string 
     */
    private function cache_file() {
        return base_path('bootstrap/cache/sunhill_classes.php');
    }
    
    /**
     * Returns true if the class cache file exists otherwise false
     * @return boolean
     */
    private function cache_exists() {
        return file_exists($this->cache_file());    
    }
    
    /**
     * Erases the class cache file. It throws an excpetion if the cache file still exists after deletion (missing rights?)
     * @throws ORMException
     */
    public function flush_cache() {
        if ($this->cache_exists()) {
            unlink($this->cache_file());
            if ($this->cache_exists()) {
                throw new ORMException("Can't delete the class cache.");
            }
        }
        $this->object_dirs = [];
    }

    /**
     * Includes all orm class file to get the classes accesible by get_declared_classes()
     * @param string $dir
     */
    private function read_object_dir(string $dir) {
        $directory = dir($dir);
        while (false !== ($entry = $directory->read())) {
            if (($entry !== '.') && ($entry !== '..')) {
                if (is_dir($dir . '/' . $entry)) {
                    $this->read_object_dir($dir . '/' . $entry);
                } else if (is_file($dir . '/' . $entry)) {
                    require_once($dir . '/' . $entry);
                }
            }
        }        
    }
    
    /**
     * After all orm class files where previously include traverse all classes and return only those children of oo_object
     * @return unknown[]
     */
    private function get_class_array() {
        $all_classes = get_declared_classes();
        $orm_classes = [];
        foreach ($all_classes as $class) {
            if (is_subclass_of($class,oo_object::class)) {
                $orm_classes[] = $class;       
            }
        }        
        return $orm_classes;
    }

    /**
     * Returns the information for the cache array
     * @param string $class The full name of a class that points to a descendant of oo_object
     */
    private function get_class_info(string $class) {
        $result = ['class'=>addslashes($class)];
        foreach ($class::$object_infos as $key => $value) {
            $result[$key] = $value;
        }
        $parent = get_parent_class($class);
        if ($class !== 'object') { // Strange infinite loop bug
            $result['parent'] = $parent::$object_infos['name'];
        }
        $result['properties'] = $this->get_class_properties($class);
        return $result;
    }
    
    private function get_class_properties(string $class) {
        $properties = $class::static_get_properties_with_feature();
        $result = [];
        foreach ($properties as $name => $descriptor) {
            if ($name !== 'tags') {
                $result[$name] = $descriptor;
            }
        }
        return $result;        
    }
    
    /**
     * Creates the class cache file
     * @param array $class_dirs
     */
    private function create_cache_file(array $class_dirs) {
        foreach ($class_dirs as $dir) {
            $this->read_object_dir($dir);
        }
        $class_array = $this->get_class_array();
        $file = fopen($this->cache_file(),'w+');
        fputs($file,"<?php return [\n".
'    "object"=>['."\n".
'       "class"=>"Sunhill\\ORM\\Objects\\oo_object",'."\n".
'       "name"=>"object",'."\n".
'       "table"=>"objects",'."\n".
'       "name_s"=>"object",'."\n".
'       "name_p"=>"objects",'."\n".
'       "description"=>"Base class for objects",'."\n".
'       "options"=>"0",'."\n".
'       "parent"=>"",'."\n".
'       "properties"=>[]],'."\n"
            );
        foreach ($class_array as $class) {
            $class_info = $this->get_class_info($class);
            fputs($file,'    "'.$class_info['name'].'"=>['."\n");
            foreach ($class_info as $key => $value) {
                if ($key == 'properties') {
                   fputs($file,'        "properties"=>['."\n");
                   foreach ($value as $prop_name => $property) {
                      $features = $property->get_static_attributes();
                      fputs($file,'          "'.$prop_name.'"=>['."\n");
                      foreach ($features as $feat_key => $feat_value) {
                          fputs($file,'             "'.$feat_key.'"=>');
                          if (is_bool($feat_value)) {
                              fputs($file,($feat_value?'true':'false').','."\n");
                          } else if (is_scalar($feat_value)) {
                              fputs($file,'"'.$feat_value.'",'."\n");
                          } else if (is_array($feat_value)) {
                              fputs($file,'[');
                              foreach ($feat_value as $single_value) {
                                  fputs($file,'"'.$single_value.'",');
                              }
                              fputs($file,'],'."\n");
                          }
                      }
                      fputs($file,'             ],'."\n");
                   }
                   fputs($file,'        ],'."\n");
                } else {
                    fputs($file,'        "'.$key.'"=>"'.$value.'",'."\n");
                }
            }
            fputs($file,"    ],\n");
        }
        fputs($file,"];");
        fclose($file);        
    }
    
    /**
     * Flushes the class cache and recreates it
     * @throws ORMException
     */
    public function create_cache($class_dir=null) {
        if (is_null($class_dir)) {
            // If not passes, set class dir to default
            $class_dir = $this->object_dirs;
        } else if (is_string($class_dir)) {
            $class_dir = [$class_dir];
        } else if (!is_array($class_dir)) {
            throw new ORMException("Unexpected data for 'class_dir'.");
        }
        $this->flush_cache();
        $this->create_cache_file($class_dir);
        if (!$this->cache_exists()) {
            throw new ORMException("Can't create the class cache.");            
        }
    }
    
    /**
     * Checks if the classes array was read from the cache. If not it reads the file
     */
    private function check_cache() {
        if (is_null($this->classes)) {
            if (!$this->cache_exists()) {
                $this->create_cache();
            }
            $this->load_cache_file();
         }
    }

    /**
     * Loads the cache file and tranlates it into a desciptor array
     */
    private function load_cache_file() {
        $classes = require($this->cache_file());
        foreach ($classes as $name => $info) {
            $descriptor = new descriptor();
            foreach ($info as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subkey => $subvalue) {
                        if (is_array($subvalue)) {
                            foreach ($subvalue as $subsubkey => $subsubvalue) {
                                $descriptor->$key->$subkey->$subsubkey = $subsubvalue;
                            }
                        } else {
                            $descriptor->$key->$subkey = $subvalue;
                        }
                    }
                } else {
                    $descriptor->$key = $value;
                }
            }
            $this->classes[$name] = $descriptor;
        }
    }
    
    /**
     * Add a single dir to the class manager, to build the class cache from
     * @param string $path
     * @throws ORMException
     */
    public function add_class_dir(string $path) {
        if (!file_exists($path)) {
            throw new ORMException("The passed class directory '$path' doesn't exists.");
        }
        $this->object_dirs[] = $path;
    }

    /**
     * Collects all data about this class to store it in the classes array
     * @param $classname string The name of the class to collect values from
     * @return array associative array with informations about this class
     */
    protected function buildClassInformation(string $classname) : array {
        $result = ['class'=>addslashes($class)];
        foreach ($class::$object_infos as $key => $value) {
            $result[$key] = $value;
        }
        $parent = get_parent_class($class);
        if ($class !== 'object') { // Strange infinite loop bug
            $result['parent'] = $parent::$object_infos['name'];
        }
        $result['properties'] = [];
        $properties = $this->get_class_properties($class);
        foreach ($properties as $property) {
            $result['properties'][$property] = [];
            $features = $property->get_static_attributes();
            foreach ($features as $feat_key => $feat_value) {
                $result['properties'][$property][$feat_key] = $feat_value;
            }            
        }    
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
        $this->classes[$classname] = $this->buildClassInformation($classname);
        return true;
    }
    
    /**
     * Clears the class information array
     */
    public function flushClasses() : void {
        $this->classes = [];
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
        $this->check_cache();
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
        $this->check_cache();
        return $this->classes;        
    }
    
    /**
     * Returns an array with the root oo_object. Each entry is an array with the name of the
     * class as key and its children as another array. 
     * Example: 
     * ['object'=>['parent_object'=>['child1'=>[],'child2'=[]],'another_parent'=>[]]
     */
    public function get_class_tree(string $class = 'object') {
        return [$class=>$this->get_children_of_class($class)];
    }
    
// *************************** Informations about a specific class **************************    
    private function not_exists($test) {
        if ($this->search_class($test)) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Normalizes the passed namespace (removes heading \ and double backslashes)
     * @param string $namespace
     * @return string
     */
    public function normalize_namespace(string $namespace) : string {
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
    public function search_class($needle) {
        $this->check_cache();
        if (is_object($needle)) {
            $needle = get_class($needle);
        }
        if (strpos($needle,'\\') !== false) {
            $needle = $this->normalize_namespace($needle);
            foreach ($this->classes as $name => $info) {
                if ($info->class === $needle) {
                    return $info->name;
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
    public function get_class_name($test) {
        if (is_int($test)) {
            return $this->get_classname_with_index($test);
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
    private function get_classname_with_index(int $index) {
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
    private function check_class($test) {
        if (is_null($test)) {
            throw new ORMException("Null was passed to check_class");
        }
        $name = $this->get_class_name($test);
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
     * @param string $name
     * @param unknown $field
     * @throws ORMException
     * @return unknown
     */
    public function get_class($test,$field=null) {
        $this->check_cache();
        $name = $this->get_class_name($test);
        $this->check_class($name);
        $class = $this->classes[$name];
        if (is_null($field)) {
            return $class;
        } else {
            if (in_array($field,static::$translatable)) {
                return $this->translate($name,$field);
            } else if ($class->is_defined($field)) {
                return $class->$field;
            } else {
                throw new ORMException("The class '$name' doesn't export '$field'.");
            }
        }        
    }
    
    /**
     * Return the table of class '$name'. Alias for get_class($name,'table')
     * @param string $name
     * @return unknown
     */
    public function get_table_of_class(string $name) {
        $name = $this->check_class($this->search_class($name));
        return $this->get_class($name,'table');        
    }
    
    /**
     * Return the parent of class '$name'. Alias for get_class($name,'parent')
     * @param string $name
     * @return unknown
     */
    public function get_parent_of_class(string $name) {
        $name = $this->check_class($this->search_class($name));
        return $this->get_class($name,'parent');
    }

    /**
     * Returns the inheritance of the given class. 
     * @param string $name
     * @param bool $include_self
     */
    public function get_inheritance_of_class(string $name,bool $include_self=false) {
        $name = $this->check_class($this->search_class($name));
        if ($include_self) {
            $result = [$name];
        } else {
            $result = [];
        }
        do {
           $name = $this->get_parent_of_class($name); 
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
        $name = $this->check_class($this->search_class($name));
        $this->check_cache();
        $result = [];
        if (!$level) { // We reached top level
            return $result;
        }
        foreach ($this->classes as $class_name => $info) {
            if ($info->parent === $name) {
                $result[$class_name] = $this->get_children_of_class($class_name,$level-1);
            }
        }
        return $result;
    }
    
    /**
     * Returns all properties of the given class
     * @param string $class The class to search for properties
     * @return descriptor of all properties
     */
    public function get_properties_of_class(string $class) {
        $name = $this->check_class($this->search_class($class));
        return $this->get_class($name,'properties');        
    }
    
    /**
     * Return only the descriptor of a given property of a given class
     * @param string $class The class to search for the property
     * @param string $property The property to search for
     * @return descriptor of this property
     */
    public function get_property_of_class(string $class,string $property) {        
        $class = $this->check_class($this->search_class($class));
        return $this->get_properties_of_class($class)->$property;        
    }
    
    /**
     * Return the full qualified namespace name of the class 'name'. Alias for get_class($name,'class')
     * @param string $name
     * @return unknown
     */
    public function get_namespace_of_class(string $name) {
        $name = $this->check_class($this->search_class($name));
        return $this->get_class($name,'class');        
    }
    
    /**
     * Creates an instance of the passes class
     * @param string $class is either the namespace or the class name 
     * @return oo_object The created instance of $class
     */
    public function create_object(string $class) {
        $class = $this->check_class($this->search_class($class));
        $namespace = $this->get_namespace_of_class($this->search_class($class));
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
        $namespace = $this->get_namespace_of_class($this->check_class($this->search_class($class)));
        return is_a($test,$namespace);
    }
    
    /**
     * Returns true is $test is exactly a $class and not of its children
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function is_a_class($test,$class) {
        $namespace = $this->get_namespace_of_class($this->check_class($this->search_class($class)));
        return is_a($test,$namespace) && !is_subclass_of($test,$namespace);
    }

    /**
     * The reimplementation of is_subclass_of() that works with class names too
     * @param unknown $test
     * @param unknown $class
     * @return boolean
     */
    public function is_subclass_of($test,$class) {
        $namespace = $this->get_namespace_of_class($this->check_class($this->search_class($class)));
        $test_space = $this->get_namespace_of_class($this->check_class($this->search_class($test)));
        return is_subclass_of($test_space,$namespace);        
    }
    
    public function migrate_class(string $class_name) {
        $class_name = $this->check_class($this->search_class($class_name));
        $migrator = new object_migrator();
        $migrator->migrate($class_name);
    }
}
