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

/**
 * This class provides methods to access information about the availble orm classes. 
 * The routines consist of:
 * - cache management (flush_cache, create_cache)
 * @author klaus
 *
 */
class class_manager {
 
    /**
     * Stores the information about the classes
     * @var array|null
     */
    private $classes=null;
    
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
        $result = ['class'=>$class];
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
            $this->classes = require_once($this->cache_file());
        }
    }
    
    /**
     * Returns the number of registered classes
     */
    public function get_class_count() {
        $this->check_cache();
        return count($this->classes);       
    }
    
}