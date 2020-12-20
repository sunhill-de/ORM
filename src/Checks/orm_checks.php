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
}