<?php

namespace Sunhill\ORM\Checks;

use Sunhill\Basic\Checker\checker;
use Illuminate\Support\Facades\DB;

class orm_checks extends checker {
    
    /**
     * Checks if all tags have existing or no parents at all
     * @return unknown
     */
    public function check_tagswithnotexistingparents() {
        $result = DB::table('tags AS a')->leftJoin('tags AS b','a.parent_id','=','b.id')->whereNull('b.id')->where('a.parent_id','>',0)->get();
        if (count($result) == 0) {
            return $this->create_result('OK','Check tags for not existing parents');
        } else {
            $first = true;
            $tag_ids = '';
            foreach ($result as $tag) {
                $tag_ids .= ($first?'':',').$tag->id;
                $first = false;
            }
            return $this->create_result('FAILED','Check tags for not existing parents','Parents of tags "'.$tag_ids.'" dont exist.');            
        }
    }
    
    /**
     * Checks if all entries in the tagcache have an existing tag
     * @return unknown
     */
    public function check_tagcachewithnotexistingtags() {
        $result = DB::table('tagcache AS a')->leftJoin('tags AS b','a.tag_id','=','b.id')->whereNull('b.id')->get();
        if (count($result) == 0) {
            return $this->create_result('OK','Check tagcache for not existing tags');
        } else {
            $first = true;
            $tag_ids = '';
            foreach ($result as $tag) {
                $tag_ids .= ($first?'':',').$tag->tag_id;
                $first = false;
            }
            return $this->create_result('FAILED','Check tagcache for not existing tags','Tags "'.$tag_ids.'" dont exist.');
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
    
    public function check_tagobjectassignstagsexist() {
        $result = DB::table('tagobjectassigns AS a')->leftJoin('tags AS b','a.tag_id','=','b.id')->whereNull('b.id')->get();
        if (count($result) == 0) {
            return $this->create_result('OK','Check tag-object-assigns for not existing tags');
        } else {
            $first = true;
            $tag_ids = '';
            foreach ($result as $tag) {
                $tag_ids .= ($first?'':',').$tag->id;
                $first = false;
            }
            return $this->create_result('FAILED','Check tag-object-assigns for not existing tags','Tags "'.$tag_ids.'" dont exist.');
        }        
    }
    
    public function check_tagobjectassignsobjectsexist() {
        $result = DB::table('tagobjectassigns AS a')->leftJoin('objects AS b','a.container_id','=','b.id')->whereNull('b.id')->get();
        if (count($result) == 0) {
            return $this->create_result('OK','Check tag-object-assigns for not existing objects');
        } else {
            $first = true;
            $tag_ids = '';
            foreach ($result as $tag) {
                $tag_ids .= ($first?'':',').$tag->container_id;
                $first = false;
            }
            return $this->create_result('FAILED','Check tag-object-assigns for not existing objects','Objects "'.$tag_ids.'" dont exist.');
        }
        
    }
}