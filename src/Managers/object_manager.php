<?php

/**
 * @file object_manager.php
 * Provides the object_manager object for accessing information about the orm objects
 * Lang en
 * Reviewstatus: 2020-09-13
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Depenencies: class_manager
 */
 namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Objects\Utils\object_promotor;
use Sunhill\ORM\Objects\Utils\object_degrader;

class ObjectManagerException extends ORMException {}

class object_manager  {
 
    protected $object_cache = [];
    
    protected function search_class_namespace($condition) {
        if (is_array($condition)) {
            if (isset($condition['class'])) {
                $condition = $condition['class'];
            } else if (isset($condition['name'])) {
                $condition = $condition['name'];
            }
        }
        $class = Classes::search_class($condition); // Mock me in tests
        if (is_null($class)) {
            throw new ObjectManagerException("Class '$condition' not found.");
        }
        return Classes::get_namespace_of_class($class);
    }
    
		/**
		 * Counts the number of objects depending on $condition
		 * if $condition is null, then every object is counted
		 * if $condition is an array with 'class' or 'namespace' as an index, then only objects of that class (this means namespace) are counted 
		 * if $condition is an array with 'name' as an index, then only object of that class (this meand name) are counted
		 * if $condition is a string that contains a backslash than the name is treated as a namespace and only objects of that namespace are returned
		 * if $condition is a string without a backslash that the name is treates as a class name and only objects of that namespace are counted
		 *     if nochildren is false (default), that derrived objects are counted too otherwise only 
		 * 		objects of this class
		 */
		public function count($condition=null,bool $nochildren=false) {
			if (is_null($condition)) {
				return $this->get_raw_count();
			} else {
                $namespace = $this->search_class_namespace($condition);
                if (!$nochildren) {
                    return $this->get_count_for_class($namespace);
                } else {
                    return $this->get_count_for_single_class($namespace);
                }
			}
		}

		private function get_raw_count() {
        	$count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
			return $count->count;
		}

		private function get_count_for_class(string $class) {
			$count = DB::table($class::$table_name)->select(DB::raw('count(*) as count'))->first();
			return $count->count;
		}

		private function get_count_for_single_class(string $class) {
			return static::get_object_list(['class'=>$class],true)->count();
		}

		/**
		 * Returns a list of objects that match to the given condition
		 */
		public function get_object_list($condition='object',bool $nochildren=false) {
		    if ($condition == 'object') {
		        $class = 'Sunhill\ORM\Objects\oo_object';
		    } else {
		      $class = $this->search_class_namespace($condition);
		    }
		    $objects = $class::search()->get();
			if ($nochildren) {
				$objects->filter_class($class,false);
			}
			return $objects;
		}

		/**
		 * Returns the name of the class with the passed $id.
		 * @param int $id ID of the object we want to know the class name of
		 * @return string The name (not the namespace!) of the class
		 */
		public function get_class_name_of(int $id) {
		    $object = DB::table('objects')->where('id','=',$id)->first();
		    if (empty($object)) {
		        return false;
		    }
		    return $object->classname;
		}

		/**
		 * Returns the namespace of the class with the passed $id.
		 * @param int $id ID of the object we want to know the class name of
		 * @return string The namespace of the class
		 */
		public function get_class_namespace_of(int $id) {
		    $object = DB::table('objects')->where('id','=',$id)->first();
		    if (empty($object)) {
		        return false;
		    }
		    return Classes::get_namespace_of_class($object->classname);
		}
		
		/**
		 * Loads the object with the id $id from the database
		 * @param int $id
		 * @return unknown|boolean
		 */
		public function load(int $id) {
		    if ($this->is_cached($id)) {
		        return $this->object_cache[$id];
		    } else {
		        if (($classname = $this->get_class_namespace_of($id)) === false) {
		            return false;
		        }
		        $object = new $classname();
		        $object = $object->load($id);
		        return $object;
		    }
		}
		
		/**
		 * Clears the object cache
		 */
		public function flush_cache() {
		    $this->object_cache = [];
		}
		
		/**
		 * Returns if the object with the id $id is in the cache
		 * @param int $id
		 * @return bool, true, wenn im Cache sonst false
		 */
		public function is_cached(int $id) {
		    return isset($this->object_cache[$id]);
		}
		
		/**
		 * Adds the entry of $id with the object $object to the cache
		 * @param int $id
		 * @param oo_objct $object
		 */
		public function insert_cache(int $id,oo_object $object) {
		    $this->object_cache[$id] = $object;
		}
		
		/**
		 * Removes the entry of $id from the cache
		 * @param int $id
		 */
		public function clear_cache(int $id) {
		    unset($this->object_cache[$id]);
		}
		
		/**
		 * Returns an instance of oo_object. If its just its id it loads the object
		 * @param unknown $object
		 * @throws ObjectManagerException
		 * @return unknown
		 */
		public function get_object($object) {
		    if (is_a($object,oo_object::class)) {
		        return $object;
		    } else if (is_int($object)) {
		        return $this->load($object);
		    } else {
		        throw new ObjectManagerException("Passed parameter is not resolvable to an object.");
		    }
		}
		
		/**
		 * Raises the given object $object to a new (and higher) class $newclass
		 * @param oo_object|int $object
		 * @param string $newclass
		 */
		public function promote_object($object,string $newclass) {
		    $promotor = new object_promotor();
		    return $promotor->promote($this->get_object($object),$newclass);
		}
		
		/**
		 * Lowers the given object $object to a new (and lower) class $newclass
		 * @param oo_object|int $object
		 * @param string $newclass
		 */
		public function degrade_object($object,string $newclass) {
		    $degrader = new object_degrader();
		    return $degrader->degrade($this->get_object($object),$newclass);
		}
		
}