<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_objects extends storagemodule_base {
    
    public function load(int $id) {
        $references = DB::table('objectobjectassigns')->where('container_id','=',$id)->get();
        if (empty($references)) {
            return;
        }
        foreach ($references as $reference) {
            if ($this->storage->get_caller()->get_property($reference->field)->has_feature('array')) {
                if (!isset($this->storage->entities[$reference->field])) {
                    $this->storage->entities[$reference->field] = [];
                }
                $this->storage->entities[$reference->field][$reference->index] = $reference->element_id;
            } else {
                $this->storage->entities[$reference->field] = $reference->element_id;
            }
        }
    }
    
}
