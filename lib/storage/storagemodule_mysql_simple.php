<?php namespace Sunhill\Storage;

use Illuminate\Support\Facades\DB;

class storagemodule_mysql_simple extends storagemodule_base {
    
    private $sorted;
    
    public function load(int $id) {
        foreach ($this->storage->get_inheritance() as $inheritance) {
            $table = $inheritance::$table_name;
            $result = DB::table($table)->where('id','=',$id)->first();
            if (!empty($result)) {
                foreach ($result as $name => $value) {
                    $this->storage->$name = $value;
                }
            } else {
                throw new StorageException("Eine ID '$id' gibt es in der Tabelle '$table' nicht.");
            }
        }  
        return $id;
    }
   
    public function prepare_insert(int $id) {
            
    }
    
    private function store_core() {
        return DB::table('objects')->insertGetId(['classname'=>get_class($this->storage->get_caller()),
                                                  'created_at'=>DB::raw('now()'),
                                                  'updated_at'=>DB::raw('now()')
        ]);
    }
    
    private function store_table($id,$table,$fields) {
        $fields['id'] = $id;
        DB::table($table)->insert($fields);
    }
    
    public function insert(int $id) {
        $id = $this->store_core();
        $fields = $this->storage->filter_storage('simple','class');
        foreach ($this->storage->get_inheritance() as $inheritance) {
            if ($inheritance == "Sunhill\\Objects\\oo_object") {
                continue;
            }
            $table = $inheritance::$table_name;
            if (!isset($fields[$inheritance])) {
                $this->store_table($id,$table,[]);
            } else {
                $this->store_table($id,$table,$fields[$inheritance]);
            }
        }
        return $id;
    }
    
    public function update(int $id) {
        
    }
    
    public function delete(int $id) {
        
    }
}
