<?php
/**
 * @file class_manager.php
 * Provides the class_manager object for accessing information about the orm classes
 * Lang en
 * Reviewstatus: 2020-09-13
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use \Sunhill\ORM\SunhillException;
use Illuminate\Support\Facades\Lang;
use Sunhill\ORM\Utils\descriptor;

/**
 * This class provides methods to access information about the availble orm classes. 
 * The routines consist of:
 * - cache management (flush_cache, create_cache)
 * @author klaus
 *
 */
class class_manager {
 
    private static $translatable = [/*'name_s','name_p','description'*/];
    
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
     * @throws SunhillException
     */
    public function flush_cache() {
        if ($this->cache_exists()) {
            unlink($this->cache_file());
            if ($this->cache_exists()) {
                throw new SunhillException("Can't delete the class cache.");
            }
        }
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
            if (is_subclass_of($class,"\\Sunhill\\ORM\\Objects\oo_object")) {
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
        fputs($file,"<?php return [\n");
        foreach ($class_array as $class) {
            $class_info = $this->get_class_info($class);
            fputs($file,'    "'.$class_info['name'].'"=>['."\n");
            foreach ($class_info as $key => $value) {
                fputs($file,'        "'.$key.'"=>"'.$value.'",'."\n");
            }
            fputs($file,"    ],\n");
        }
        fputs($file,"];");
        fclose($file);        
    }
    
    /**
     * Flushes the class cache and recreates it
     * @throws SunhillException
     */
    public function create_cache($class_dir=null) {
        if (is_null($class_dir)) {
            // If not passes, set class dir to default
            $class_dir = [base_path('objects')];
        } else if (is_string($class_dir)) {
            $class_dir = [$class_dir];
        } else if (!is_array($class_dir)) {
            throw new SunhillException("Unexpected data for 'class_dir'.");
        }
        $this->flush_cache();
        $this->create_cache_file($class_dir);
        if (!$this->cache_exists()) {
            throw new SunhillException("Can't create the class cache.");            
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
                $descriptor->$key = $value;
            }
            $this->classes[$name] = $descriptor;
        }
    }
    
// *************************** General class informations ===============================    
    /**
     * Returns the number of registered classes
     */
    public function get_class_count() {
        $this->check_cache();
        return count($this->classes);       
    }

    /**
     * Returns a treversable associative array of all registered classes
     * @return unknown
     */
    public function get_all_classes() {
        $this->check_cache();
        return $this->classes;
    }
    
// *************************** Informations about a specific class **************************    
    private function normalize_namespace(string $namespace) : string {
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
     * @param string $needle
     */
    public function search_class(string $needle) {
        $this->check_cache();
        if (strpos($needle,'\\') !== false) {
            $needle = $this->normalize_namespace($needle);
            foreach ($this->classes as $name => $info) {
                if ($info->class === $needle) {
                    return $info->name;
                }
            }
            return null;
        }  else {
            if (isset($this->classes[$needle])) {
                return $needle;
            } else {
                return null;
            }
        }
    }
    
    private function check_class(string $name) {
        if (!isset($this->classes[$name])) {
            throw new SunhillException("The class '$name' doesn't exists.");
        }
    }
    
    private function translate(string $class,string $item) {
        return Lang::get('ORM:testfiles.'.$class.'_'.$item);
    }
    
    /**
     * Searches for the class named '$name'
     * @param string $name
     * @param unknown $field
     * @throws SunhillException
     * @return unknown
     */
    public function get_class($name,$field=null) {
        $this->check_cache();
        if (is_int($name)) {
            if ($name < 0) {
                throw new SunhillException("Invalid Index '$name'");
            }
            $i=0;
            foreach ($this->classes as $class_name => $info) {
                if ($i==$name) {
                    if (is_null($field)) {
                        return $info;
                    } else {
                        return $info->$field;                        
                    }
                }
                $i++;
            }
            throw new SunhillException("Invalid index '$name'");
        } else {
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
                    throw new SunhillException("The class '$name' doesn't export '$field'.");
                }
            }
        }
    }
    
    /**
     * Return the table of class '$name'. Alias for get_class($name,'table')
     * @param string $name
     * @return unknown
     */
    public function get_table_of_class(string $name) {
        return $this->get_class($name,'table');        
    }
    
}