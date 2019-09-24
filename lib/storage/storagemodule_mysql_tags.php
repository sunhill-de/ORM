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
    }
    
}
