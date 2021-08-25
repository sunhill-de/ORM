<?php

/**
 * @file orm_checks.php
 * An extension to the sunhill check system to perform checks on the sunhill orm database
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-08-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/ORMCheckTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Checks;

use Sunhill\Basic\Checker\checker;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;

/**
 * Provides checks for the checking subsystem of sunhill for the orm system
 * @author klaus
 *
 */
class orm_checks extends checker {
    
    /**
     * Helper function for the check for tables that point to non existing entries
     */
    protected function check_for_dangling_pointers(string $master,string $master_field,string $slave,string $slave_field,$master_can_be_null=false) {
        $query = DB::table($master.' AS a')->select('a.'.$master_field.' as id')->leftJoin($slave.' AS b','a.'.$master_field,'=','b.'.$slave_field)->whereNull('b.'.$slave_field);    
        if ($master_can_be_null) {
            $query = $query->where('a.'.$master_field,'>',0);
        }
        $query_result = $query->get();
        if (count($query_result)) {
            $result = '';
            foreach ($query_result as $entry) {
                $result .= (empty($result)?$entry->$slave_field:','.$entry->$slave_field);
            }
            return $result;
        } else {
            return null;
        }
    }
    
    /**
     * Checks if all tags have existing or no parents at all
     * @return unknown
     */
    public function check_tagswithnotexistingparents() {
        if ($entries = $this->check_for_dangling_pointers('tags','parent_id','tags','id',true)) {
            return $this->create_result(__('FAILED'),__('Check tags for not existing parents'),__("Parents of tags ':entries' dont exist.",['entries'=>$entries]));            
        } else {
            return $this->create_result(__('OK'),__('Check tags for not existing parents'));            
        }
    }
    
    /**
     * Checks if all entries in the tagcache have an existing tag
     * @return unknown
     */
    public function check_tagcachewithnotexistingtags() {
        if ($entries = $this->check_for_dangling_pointers('tagcache','tag_id','tags','id')) {
            return $this->create_result(__('FAILED'),__("Check tagcache for not existing tags"),__("Tags ':entries' dont exist.",['entries'=>$entries]));            
        } else {
            return $this->create_result(__('OK'),__('Check tagcache for not existing tags'));
            
        }        
    }
    
    private function get_tag($tags,$id) {
        foreach ($tags as $tag) {
            if ($tag->id == $id) {
                return $tag;
            }
        }
        return null;
    }
    
    private function build_tag_row(&$result,$tags,$tag,$postfix='') {
            $result[] = $tag->name.$postfix;
            if ($newtag = $this->get_tag($tags,$tag->parent_id)) {
                $this->build_tag_row($result,$tags,$newtag,'.'.$tag->name.$postfix);
            }
    }
    
    private function build_cache(&$result,$tags) {
        foreach ($tags as $tag) {
            $this->build_tag_row($result,$tags,$tag);
        }
    }
    
    /**
     * Checks if the number of entries in the tagcache is correct and if all entries in the tagcache are right 
     * @return unknown
     */
    public function check_tagcacheconsistency() {
        $tags = DB::table('tags')->get();
        $result = [];        
        $this->build_cache($result,$tags);
        $count = DB::table('tagcache')->count();
        if ($count !== count($result)) {
            return $this->create_result(__('FAILED'),__('Check tagcache consitency'),__("Entry count :count doenst match expected :expect",['count'=>$count,'expect'=>count($result)]));            
        }
        $tagcache_entries = DB::table('tagcache')->get();
        $entries = '';
        foreach ($tagcache_entries as $entry) {
            if (!in_array($entry->name,$result)) {
                $entries .= (empty($entries)?$entry->name:','.$entry->name);
            }
        }
        if (empty($entries)) {
            return $this->create_result(__('OK'),__('Check tagcache consitency'));            
        } else {
            return $this->create_result(__('FAILED'),__('Check tagcache consitency'),__("Entries :entries don't match.",['entries'=>$entries]));            
        }
    }
    
    /**
     * Checks if all tags in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_tagobjectassignstagsexist() {
        if ($entries = $this->check_for_dangling_pointers('tagobjectassigns','tag_id','tags','id',true)) {
            return $this->create_result(__('FAILED'),__('Check tag-object-assigns for not existing tags'),__("Tags ':entries' dont exist.",['entries'=>$entries]));
        } else {
            return $this->create_result(__('OK'),__('Check tag-object-assigns for not existing tags'));
        }
    }
    
    /**
     * Checks if all objects in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_tagobjectassignsobjectsexist() {
        if ($entries = $this->check_for_dangling_pointers('tagobjectassigns','container_id','objects','id',true)) {
            return $this->create_result(__('FAILED'),__('Check tag-object-assigns for not existing objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries));
        } else {
            return $this->create_result(__('OK'),__('Check tag-object-assigns for not existing objects'));
        }
    }
    
    /**
     * Checks if all container objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_objectobjectassignscontainerexist() {
        if ($entries = $this->check_for_dangling_pointers('objectobjectassigns','container_id','objects','id',true)) {
            return $this->create_result(__('FAILED'),__('Check object-object-assigns for not existing container objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            return $this->create_result(__('OK'),__('Check object-object-assigns for not existing container objects'));
        }
    }
    
    /**
     * Checks if all element objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_objectobjectassignselementexist() {
        if ($entries = $this->check_for_dangling_pointers('objectobjectassigns','element_id','objects','id',true)) {
            return $this->create_result(__('FAILED'),__('Check object-object-assigns for not existing element objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            return $this->create_result(__('OK'),__('Check object-object-assigns for not existing element objects'));
        }
    }
    
    /**
     * Checks if all container objects in the stringobjectassigns table exists
     * @return unknown
     */
    public function check_stringobjectassignscontainerexist() {
        if ($entries = $this->check_for_dangling_pointers('stringobjectassigns','container_id','objects','id',true)) {
            return $this->create_result(__('FAILED'),__('Check string-object-assigns for not existing container objects'),__("Objects ':entries' dont exist.",array('entries'=>$entries)));
        } else {
            return $this->create_result(__('OK'),__('Check string-object-assigns for not existing container objects'));
        }
    }
    
    /**
     * Checks if all classes in objects exist
     * @return unknown
     */
    public function check_objectexistance() {
        $tables = DB::table('objects')->distinct('classname')->get();
        $bad_classes = '';
        foreach ($tables as $table) {
            if (!Classes::search_class($table->classname)) {
                $bad_classes .= (empty($bad_classes)?'':', ').$table->classname;
            }
        }
        if (empty($bad_classes)) {
            return $this->create_result(__('OK'),__('Check for non existance classes in objects'));            
        } else {
            return $this->create_result(__('FAILED'),__('Check for non existance classes in objects'),__("Classes ':bad_classes' dont exist.",['bad_classes'=>$bad_classes]);            
        }
    }
    
    public function check_classtablegaps() {
        $table_tree = $this->get_table_tree();
        $return = '';
        
        foreach ($table_tree as $master=>$table) {
            if ($result = $this->test_table($master,$table)) {
                $return .= (empty($return)?'':',').$result;        
            }
        }
        
        if (empty($return)) {
            return $this->create_result(__('OK'),__('Check for gaps in object tables'));
        } else {
            return $this->create_result(__('FAILED'),__('Check for gaps in object tables'),__("Objects ':return' have gaps.",['return'=>$return]));
        }
    }
    
    private function table_exists(string $table) {
        $query = array_map('reset',DB::select('show tables'));    
        return in_array($table,$query);
    }
    
    private function test_table(string $master,array $tables) {
        if (!$this->table_exists($master)) {
            return;
        }
        $query = DB::table($master);
        foreach ($tables as $table) {
            $query = $query->leftJoin($table,$table.'.id','=',$master.'.id');
            $query = $query->orWhere("$table.id",null);
        }
        $result = $query->get();
        if ($result->count() > 0) {
            return $master;
        }
    }
    
    private function get_subtables(string $class) {        
        $parent_classes = Classes::get_inheritance_of_class($class);
        $result = [];
        
        foreach ($parent_classes as $parent) {
            $result[] = Classes::get_table_of_class($parent);
        }
        
        return $result;
    }
    
    private function get_table_tree() {
        $classes = Classes::get_all_classes();
        $tables = [];
        foreach ($classes as $class) {
            if ($class->name !== 'object') {
                $tables[Classes::get_table_of_class($class->name)] = $this->get_subtables($class->name);
            }
        }
        
        return $tables;
    }
}
