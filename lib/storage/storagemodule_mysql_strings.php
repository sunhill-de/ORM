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
        return $id;
    }
    
    public function insert(int $id) {
        $inserts = [];
        $properties = $this->storage->filter_storage('strings');
        foreach ($properties as $property=>$values) {
            foreach ($values as $index=>$value) {
                $inserts[] = ['container_id'=>$id,'element_id'=>$value,'field'=>$property,'index'=>$index];
            }
        }
        DB::table('stringobjectassigns')->insert($inserts);
        return $id;
    }
    
    public function update(int $id) {
        
    }
    
    public function delete(int $id) {
        DB::table('stringobjectassigns')->where('container_id','=',$id);  
    }
}
