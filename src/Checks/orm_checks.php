<?php

namespace Sunhill\ORM\Checks;

use Sunhill\Basic\Checker\checker;
use Illuminate\Support\Facades\DB;

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
            return $this->create_result('FAILED','Check tags for not existing parents',"Parents of tags '$entries' dont exist.");            
        } else {
            return $this->create_result('OK','Check tags for not existing parents');            
        }
    }
    
    /**
     * Checks if all entries in the tagcache have an existing tag
     * @return unknown
     */
    public function check_tagcachewithnotexistingtags() {
        if ($entries = $this->check_for_dangling_pointers('tagcache','tag_id','tags','id')) {
            return $this->create_result('FAILED',"Check tagcache for not existing tags","Tags '$entries' dont exist.");            
        } else {
            return $this->create_result('OK','Check tagcache for not existing tags');
            
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
            return $this->create_result('FAILED','Check tagcache consitency',"Entry count $count doenst match expected ".count($result));            
        }
        $tagcache_entries = DB::table('tagcache')->get();
        $entries = '';
        foreach ($tagcache_entries as $entry) {
            if (!in_array($entry->name,$result)) {
                $entries .= (empty($entries)?$entry->name:','.$entry->name);
            }
        }
        if (empty($entries)) {
            return $this->create_result('OK','Check tagcache consitency');            
        } else {
            return $this->create_result('FAILED','Check tagcache consitency',"Entries $entries don't match.");            
        }
    }
    
    /**
     * Checks if all tags in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_tagobjectassignstagsexist() {
        if ($entries = $this->check_for_dangling_pointers('tagobjectassigns','tag_id','tags','id',true)) {
            return $this->create_result('FAILED','Check tag-object-assigns for not existing tags',"Tags '$entries' dont exist.");
        } else {
            return $this->create_result('OK','Check tag-object-assigns for not existing tags');
        }
    }
    
    /**
     * Checks if all objects in the tagobjectassigns table exists
     * @return unknown
     */
    public function check_tagobjectassignsobjectsexist() {
        if ($entries = $this->check_for_dangling_pointers('tagobjectassigns','container_id','objects','id',true)) {
            return $this->create_result('FAILED','Check tag-object-assigns for not existing objects',"Objects '$entries' dont exist.");
        } else {
            return $this->create_result('OK','Check tag-object-assigns for not existing objects');
        }
    }
    
    /**
     * Checks if all container objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_objectobjectassignscontainerexist() {
        if ($entries = $this->check_for_dangling_pointers('objectobjectassigns','container_id','objects','id',true)) {
            return $this->create_result('FAILED','Check object-object-assigns for not existing container objects',"Objects '$entries' dont exist.");
        } else {
            return $this->create_result('OK','Check object-object-assigns for not existing container objects');
        }
    }
    
    /**
     * Checks if all element objects in the objectobjectassigns table exists
     * @return unknown
     */
    public function check_objectobjectassignselementexist() {
        if ($entries = $this->check_for_dangling_pointers('objectobjectassigns','element_id','objects','id',true)) {
            return $this->create_result('FAILED','Check object-object-assigns for not existing element objects',"Objects '$entries' dont exist.");
        } else {
            return $this->create_result('OK','Check object-object-assigns for not existing element objects');
        }
    }
    
}