<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_tags extends storagemodule_base {
    
    public function load(int $id) {
        $assigns = DB::table('tagobjectassigns')->where('container_id','=',$id)->get();
        if (empty($assigns)) {
            return;
        }
        foreach ($assigns as $assign) {
            $this->storage->entities['tags'][] = $assign->tag_id;
        }
        return $id;
    }
    
    public function insert(int $id) {
        $inserts = [];
        if (is_null($this->storage->tags)) {
            return $id;
        }
        foreach ($this->storage->tags as $tag) {
            $inserts[] = ['container_id'=>$id,'tag_id'=>$tag];
        }
        DB::table('tagobjectassigns')->insert($inserts);
        return $id; 
    }
    
    public function update(int $id) {
        if (is_null($this->storage->tags)) {
            return $id;
        }
        $this->delete($id);
        $this->insert($id);
        return $id;
    }
    
    public function delete(int $id) {
        DB::table('tagobjectassigns')->where('container_id','=',$id)->delete();
        return $id;
    }
    
}
