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
 */
 namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\SunhillException;

class ObjectManagerException extends SunhillException {}

class object_manager  {
 
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
			} else if (is_array($condition)){
				if (isset($condition['class'])) {
					$class = $condition['class'];
					if (!class_exists($class)) {
						throw new ObjectManagerException("Unknown class '$class'");
					}
					if (!$nochildren) {
						return $this->get_count_for_class($class);
					} else {
						return $this->get_count_for_single_class($class);
					}
				} else {
					throw new ObjectManagerException("Unknown condition.");
				}
			}
		}

		private function get_raw_count() {
        	$count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
			return $count->count;
		}

		private static function get_count_for_class(string $class) {
			$count = DB::table($class::$table_name)->select(DB::raw('count(*) as count'))->first();
			return $count->count;
		}

		private static function get_count_for_single_class(string $class) {
			return static::get_object_list(['class'=>$class],true)->count();
		}

		/**
		 * Returns a list of objects that match to the given condition
		 */
		public static function get_object_list($condition=['class'=>'\Sunhill\ORM\Objects\oo_object'],bool $nochildren=false) {
			if (isset($condition['class'])) {
				$class = $condition['class'];
			} else {
				throw new ObjectManagerException("Can't list without classname.");
			}
			$objects = static::convert($class::search()->get());
			if ($nochildren) {
				$objects->filter_class($class,false);
			}
			return $objects;
		}

		/**
		 * Takes the result of an /Sunhill/query_builder and converts it into a object_list
		 * @todo when objectlist is moved to sunnhill, this function is depecrated or obsolete
		 */
		public static function convert($query_result) {
			$result = new \Manager\Utils\objectlist();
			if (is_int($query_result)) {
				$result[] = $query_result;

			} else if (is_array($query_result)) {
				foreach ($query_result as $id) {
					$result[] = $id;
				}
			}
			return $result;
		}
 }