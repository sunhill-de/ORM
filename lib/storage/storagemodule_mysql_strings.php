<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_strings extends storagemodule_base {
    
    public function load(int $id) {
        $references = DB::table('stringobjectassigns')->where('container_id','=',$id)->get();
        if (empty($references)) {
            return;
        }
        foreach ($references as $reference) {
            if (!isset($this->storage->entities[$reference->field])) {
                $this->storage->entities[$reference->field] = [];
            }
            $this->storage->entities[$reference->field][$reference->index] = $reference->element_id;
        }
    }
    
}
