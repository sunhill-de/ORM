<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_externalhooks extends storagemodule_base {
    
    public function load(int $id) {
        $hooks = DB::table('externalhooks')->where('container_id','=',$id)->get();
        if (empty($hooks)) {
            return;
        }
        foreach($hooks as $hook) {
            $line = [];
            foreach ($hook as $key => $value) {
                $line[$key] = $value;
            }
            $this->storage->entities['externalhooks'][] = $line;
        }
        return $id;
    }
    
    public function insert(int $id) {
        return $id;
    }
    
    public function update(int $id) {
        
    }
    
    public function delete(int $id) {
        
    }
}
