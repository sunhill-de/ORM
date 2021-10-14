<?php
 
/**
 * @file ObjectManager.php
 * Provides the ObjectManager object for accessing information about the orm objects
 * @author Klaus Dimde
 * -----------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-10-11
 * Localization: unknown
 * Documentation: complete
 * Tests: tests/Unit/Managers/ManagerObjectTest.php
 * Coverage: unknown
 * Depenencies: ClassManager
 * PSR-State: complete
 */
 namespace Sunhill\ORM\Managers;

use Illuminate\Support\Facades\DB;

use Sunhill\ORM\ORMException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Utils\ObjectPromotor;
use Sunhill\ORM\Objects\Utils\ObjectDegrader;

class ObjectManagerException extends ORMException {}

class ObjectManager  
{
 
    protected $object_cache = [];
    
    protected function searchClassNamespace($condition): string 
    {
        if (is_array($condition)) {
            if (isset($condition['class'])) {
                $condition = $condition['class'];
            } else if (isset($condition['name'])) {
                $condition = $condition['name'];
            }
        }
        $class = Classes::searchClass($condition); // Mock me in tests
        if (is_null($class)) {
            throw new ObjectManagerException(__("Class ':condition' not found.",['condition'=>$condition]));
        }
        return Classes::getNamespaceOfClass($class);
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
		public function count($condition = null, bool $nochildren = false): int 
		{
			if (is_null($condition)) {
				return $this->getRawCount();
			} else {
                $namespace = $this->searchClassNamespace($condition);
                if (!$nochildren) {
                    return $this->getCountForClass($namespace);
                } else {
                    return $this->getCountForSingleClass($namespace);
                }
			}
		}

		private function getRawCount(): int 
		{
        	$count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
			return $count->count;
		}

		private function getCountForClass(string $class): int 
		{
			$count = DB::table($class::$table_name)->select(DB::raw('count(*) as count'))->first();
			return $count->count;
		}

		private function getCountForSingleClass(string $class): int 
		{
			return static::getObjectList(['class'=>$class],true)->count();
		}

		/**
		 * Returns a list of objects that match to the given condition
		 */
		public function getObjectList($condition = 'object', bool $nochildren = false): array 
		{
		    if ($condition == 'object') {
		        $class = 'Sunhill\ORM\Objects\ORMObject';
		    } else {
		      $class = $this->searchClassNamespace($condition);
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
		public function getClassNameOf(int $id): string 
		{
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
		public function getClassNamespaceOf(int $id): string 
		{
		    $object = DB::table('objects')->where('id','=',$id)->first();
		    if (empty($object)) {
		        return false;
		    }
		    return Classes::getNamespaceOfClass($object->classname);
		}
		
		/**
		 * Loads the object with the id $id from the database
		 * @param int $id
		 * @return unknown|boolean
		 */
		public function load(int $id): ORMObject 
		{
		    if ($this->is_cached($id)) {
		        return $this->object_cache[$id];
		    } else {
		        if (($classname = $this->getClassNamespaceOf($id)) === false) {
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
		public function flushCache(): null 
		{
		    $this->object_cache = [];
		}
		
		/**
		 * Returns if the object with the id $id is in the cache
		 * @param int $id
		 * @return bool, true, wenn im Cache sonst false
		 */
		public function isCached(int $id): bool 
		{
		    return isset($this->object_cache[$id]);
		}
		
		/**
		 * Adds the entry of $id with the object $object to the cache
		 * @param int $id
		 * @param oo_objct $object
		 */
		public function insertCache(int $id, ORMObject $object): null 
		{
		    $this->object_cache[$id] = $object;
		}
		
		/**
		 * Removes the entry of $id from the cache
		 * @param int $id
		 */		
		public function clearCache(int $id): null 
		{
		    unset($this->object_cache[$id]);
		}
		
		/**
		 * Returns an instance of ORMObject. If its just its id it loads the object
		 * @param unknown $object
		 * @throws ObjectManagerException
		 * @return unknown
		 */
		public function getObject(ORMObject|int $object): ORMObject 
		{
		    if (is_a($object,ORMObject::class)) {
		        return $object;
		    } else if (is_int($object)) {
		        return $this->load($object);
		    } else {
		        throw new ObjectManagerException(__("Passed parameter is not resolvable to an object."));
		    }
		}
		
		/**
		 * Raises the given object $object to a new (and higher) class $newclass
		 * @param ORMObject|int $object
		 * @param string $newclass
		 */
		public function promoteObject(int|ORMObject $object,string $newclass): ORMObject 
		{
		    $promotor = new ObjectPromotor();
		    return $promotor->promote($this->getObject($object),$newclass);
		}
		
		/**
		 * Lowers the given object $object to a new (and lower) class $newclass
		 * @param ORMObject|int $object
		 * @param string $newclass
		 */
		public function degradeObject($object,string $newclass): ORMObject 
		{
		    $degrader = new ObjectDegrader();
		    return $degrader->degrade($this->getObject($object),$newclass);
		}

        /**
         * Deletes alls objects of the given class from the database
         */
        public function clearObjects($class): null 
        {
            $inheritance = Classes::getInheritanceOfClass($class,false);
            $master = Classes::getTableOfClass($class);
            foreach ($inheritance as $subclass) {
                $table = Classes::getTableOfClass($subclass);
                DB::statement("delete from $table where id in (select id from $master)");
            }
            DB::statement("delete from tagobjectassigns where container_id in (select id from $master)");
            DB::statement("delete from stringobjectassigns where container_id in (select id from $master)");
            DB::statement("delete from objectobjectassigns where container_id in (select id from $master)");
            DB::statement("delete from objectobjectassigns where element_id in (select id from $master)");
            DB::table($master)->delete();
        }    
}
