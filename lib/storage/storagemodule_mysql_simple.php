<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_simple extends storagemodule_base {
    
    public function load(int $id) {
        foreach ($this->storage->get_inheritance() as $inheritance) {
            $table = $inheritance::$table_name;
            $result = DB::table($table)->where('id','=',$id)->first();
            if (!empty($result)) {
                foreach ($result as $name => $value) {
                    $this->storage->$name = $value;
                }
            } else {
                throw new StorageException("Eine ID '".$this->entities['id']."' gibt es in der Tabelle '$table' nicht.");
            }
        }        
    }
    
}
