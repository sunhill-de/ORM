<?php

/**
 * @file object_degrader.php
 * Provides the object_degrader class that is a supporting class for the object manager
 * Lang en
 * Reviewstatus: 2020-10-22
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Classes
 */
namespace Sunhill\ORM\Objects\Utils;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Managers\ObjectManagerException;

class object_degrader {
 
    public function degrade(oo_object $object,string $newclass) {
        $original_name = Classes::get_class_name($object);
        $new_name = Classes::get_class_name($newclass);
        
        if (empty($new_name)) {
            throw new ObjectManagerException("The class '$newclass' doesn't exist.");
        }
        $this->original_namespace = Classes::get_namespace_of_class($original_name);
        if (!Classes::is_subclass_of($original_name,$new_name)) {
            throw new ObjectManagerException("'$original_name' is not a subclass of '$new_name'");
        }
        $object->pre_degration($new_name);
        $newobject = $this->degration($object,$new_name);
        $newobject->post_degration($object);
  //      $object->set_state('invalid'); // The old object is invalid
        return $newobject;
    }
    
    private function get_class_diff($hiclass,$loclass) {
        $hi_hirarchy = Classes::get_inheritance_of_class($hiclass);
        $lo_hirarchy = Classes::get_inheritance_of_class($loclass);
        return array_diff($hi_hirarchy,$lo_hirarchy);
    }
    
    private function get_affected_fields($object,$storage,array $diff) {
        foreach($this->properties as $property) {
            if (in_array($property->get_class(),$diff)) {
                $storage->set_entity($property->get_name(),1);
            }
        }
    }
    
    protected function get_used_tables_of_class(string $class) {
        $result = [];
        while ($class !== 'object') {
            $result[] = Classes::get_table_of_class($class);
            $class = Classes::get_parent_of_class($class);
        }
        return $result;
    }
    
    protected function clean_simple_tables(int $id,string $high,string $low) {
        // Lost tables stores all tables that have no entries due this degration
        $lost_tables = array_diff($this->get_used_tables_of_class($high),$this->get_used_tables_of_class($low));
        foreach ($lost_tables as $table) {
            DB::table($table)->where('id',$id)->delete();
        }
    }
    
    protected function get_descriptor_diff($desc1,$desc2) {
        $arr_1 = [];
        foreach ($desc1 as $entry) {
            if (($entry->type == 'object') || ($entry->type == 'array_of_strings') || ($entry->type == 'array_of_objects')) {
                $arr_1[] = $entry->name;
            }
        }
        $arr_2 = [];
        foreach ($desc2 as $entry) {
            if (($entry->type == 'object') || ($entry->type == 'array_of_strings') || ($entry->type == 'array_of_objects')) {
                $arr_2[] = $entry->name;
            }
        }
        return array_diff($arr_1,$arr_2);
    }
    
    protected function clean_complex_tables(int $id,string $high,string $low) {
        $high_props = Classes::get_properties_of_class($high);
        $low_props = Classes::get_properties_of_class($low);
        $lost_fields = $this->get_descriptor_diff($high_props,$low_props);
        foreach ($lost_fields as $field) {
            switch ($high_props->$field->type) {
                case 'array_of_strings':
                    DB::table('stringobjectassigns')->where('field',$field)->where('container_id',$id)->delete();
                    break;
                case 'array_of_objects':
                case 'object':
                    DB::table('objectobjectassigns')->where('field',$field)->where('container_id',$id)->delete();
                    break;
                case 'calculated':
                    DB::table('caching')->where('field',$field)->where('object_id',$id)->delete();
                    break;
            }
        }
    }
    
    protected function clean_tables(int $id,string $high,string $low) {
        $this->clean_simple_tables($id,$high,$low);
        $this->clean_complex_tables($id,$high,$low);
    }
    
    protected function degration(oo_object $object,String $newclass) {
        $newclass = Classes::get_class_name($newclass);
        $namespace = Classes::get_namespace_of_class($newclass);
        $newobject = new $namespace; // Create a new object
        $newobject->set_id($object->get_id());
        $newobject->copy_from($object); // Copy all properties of the degraded object from the higher one
        DB::table('objects')->where('id','=',$object->get_id())->update(['classname'=>Classes::get_class_name($newclass)]);
        $this->clean_tables($object->get_id(),Classes::get_class_name($object),Classes::get_class_name($newclass));
        $newobject->recalculate();
        $newobject->clean_properties();
        return $newobject;
        
    }
    
    
}
