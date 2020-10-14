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
use Sunhill\ORM\SunhillException;
use Sunhill\ORM\Facades\Classes;

class ObjectManagerException extends SunhillException {}

class object_manager  {
 
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
		    $object = $this->get_class_namespace_of($id); 
		    DB::table('objects')->where('id','=',$id)->first();
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
		
		
 }