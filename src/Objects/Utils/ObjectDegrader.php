<?php

/**
 * @file ObjectDegrader.php
 * Provides the ObjectDegrader class that is a supporting class for the object manager
 * Lang en
 * Reviewstatus: 2021-10-06
 * Localization: complete
 * Documentation: complete
 * Tests: Feature\Objects\Utils\ObjectDegradeTest.php
 * Coverage: unknown
 * Dependencies: Classes
 * PSR-State: complete
 */
namespace Sunhill\ORM\Objects\Utils;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Managers\ObjectManagerException;

class ObjectDegrader {
 
    public function degrade(ORMObject $object, string $newclass): ORMObject
    {
        $original_name = Classes::getClassName($object);
        $new_name = Classes::getClassName($newclass);
        
        if (empty($new_name)) {
            throw new ObjectManagerException(__("The target class ':newclass' doesn't exist.",['newclass'=>$newclass]));
        }
        $this->original_namespace = Classes::getNamespaceOfClass($original_name);
        if (!Classes::isSubclassOf($original_name,$new_name)) {
            throw new ObjectManagerException(__("':oldclass' is not a subclass of ':newclass'",['oldclass'=>$original_name,'newclass'=>$new_name]));
        }
        $object->preDegration($new_name);
        $newobject = $this->degration($object,$new_name);
        $newobject->postDegration($object);
  //      $object->setState('invalid'); // The old object is invalid
        return $newobject;
    }
    
    /**
     * Returns all simple field tables that are used by this class.
     * @param $class string The name of the class to detect the tables from
     * @return array of string: The simple field tables that are used by the given class
     */
    protected function getUsedTablesOfClass(string $class): array
    {
        $result = [];
        while ($class !== 'object') {
            $result[] = Classes::getTableOfClass($class);
            $class = Classes::getParentOfClass($class);
        }
        return $result;
    }

    /**
     * Removed all entries in simple field tables that are lost in the process of degration
     * @param $id int: the id of the object
     * @param $hight string: The name of the higher (more away from the root object) class
     * @param $low string: The name of the lower (nearer to the root object) class
     */
    protected function cleanSimpleTables(int $id, string $high, string $low): null
    {
        // Lost tables stores all tables that have no entries due this degration
        $lost_tables = array_diff($this->getUsedTablesOfClass($high),$this->getUsedTablesOfClass($low));
        foreach ($lost_tables as $table) {
            DB::table($table)->where('id',$id)->delete();
        }
    }
    
    /**
     * Calculates the difference in complex properties between the given Descriptor arrays
     * @param $desc1 The first Descriptor array
     * @param $desc2 The second Descriptor array
     * @return array The difference between these regarding to complex fields
     */
    protected function getDescriptorDiff(array $desc1, array $desc2): array
    {
        $arr_1 = [];
        foreach ($desc1 as $entry) {
            if (($entry['type'] == 'object') || ($entry['type'] == 'arrayOfStrings') || ($entry['type'] == 'arrayOfObject')) {
                $arr_1[] = $entry['name'];
            }
        }
        $arr_2 = [];
        foreach ($desc2 as $entry) {
            if (($entry['type'] == 'object') || ($entry['type'] == 'arrayOfStrings') || ($entry['type'] == 'arrayOfObject')) {
                $arr_2[] = $entry['name'];
            }
        }
        return array_diff($arr_1,$arr_2);
    }
    
    /**
     * Removes the entries from the complex tables
     * @param $id int: The id of the object
     * @param $high string: The name of the higher class
     * @param $low string: The name of the lower class
     */
    protected function cleanComplexTables(int $id, string $high, string $low): null
    {
        $high_props = Classes::getPropertiesOfClass($high);
        $low_props = Classes::getPropertiesOfClass($low);
        
        $lost_fields = $this->getDescriptorDiff($high_props,$low_props);
        foreach ($lost_fields as $field) {
            switch ($high_props[$field]['type']) {
                case 'arrayOfStrings':
                    DB::table('stringobjectassigns')->where('field',$field)->where('container_id',$id)->delete();
                    break;
                case 'arrayOfObject':
                case 'object':
                    DB::table('objectobjectassigns')->where('field',$field)->where('container_id',$id)->delete();
                    break;
                case 'calculated':
                    DB::table('caching')->where('field',$field)->where('object_id',$id)->delete();
                    break;
            }
        }
    }

    /**
     * Cleans the database from the unneccesary tables
     * @param $id int: The id of the object
     * @param $high string: The name of the higher class
     * @param $low string: The name of the lower class
     */
    protected function cleanTables(int $id,string $high,string $low) 
    {
        $this->cleanSimpleTables($id,$high,$low);
        $this->cleanComplexTables($id,$high,$low);
    }
    
    /**
     * Does the degration
     * @param $object ORMObject The object to degrade
     * @param $newclass string The new taregt class
     * @return ORMObject
     */
    protected function degration(ORMObject $object,String $newclass) 
    {
        $newclass = Classes::getClassName($newclass);
        $namespace = Classes::getNamespaceOfClass($newclass);
        $newobject = new $namespace; // Create a new object
        $newobject->setID($object->getID());
        $newobject->copyFrom($object); // Copy all properties of the degraded object from the higher one
        
        DB::table('objects')->where('id','=',$object->getID())->update(['classname'=>Classes::getClassName($newclass)]);
        $this->cleanTables($object->getID(),Classes::getClassName($object),Classes::getClassName($newclass));
        
        $newobject->recalculate();
        $newobject->cleanProperties();
        
        return $newobject;        
    }
    
    
}
