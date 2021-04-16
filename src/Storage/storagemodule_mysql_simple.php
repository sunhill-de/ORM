<?php 
/**
 * @file storagemodule_mysql_simple.php
 * Provides the storage module that uses mysql for simple properties
 * Lang en
 * Reviewstatus: 2020-09-11
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 */

namespace Sunhill\ORM\Storage;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;

class storagemodule_mysql_simple extends storagemodule_base {
    
    private $sorted;
    
    public function load(int $id) {
        foreach ($this->storage->get_inheritance() as $inheritance) {
            $table = ($inheritance=='object')?'objects':Classes::get_table_of_class($inheritance); 
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
        return DB::table('objects')->insertGetId(['classname'=>Classes::get_class_name($this->storage->get_caller()),
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
            if ($inheritance == "object") {
                return $id;
            }
            $table = Classes::get_table_of_class($inheritance);
            if (!isset($fields[$inheritance])) {
                $this->store_table($id,$table,[]);
            } else {
                $this->store_table($id,$table,$fields[$inheritance]);
            }
        }
        return $id;
    }
    
    private function update_core(int $id) {
        DB::table('objects')->where('id',$id)->update(['updated_at'=>DB::raw('now()')]);
    }
    
    private function update_table($id,$table,$fields) {
        $change = array();
        foreach ($fields as $field=>$diff) {
            $change[$field] = $diff['TO'];
        }
        DB::table($table)->updateOrInsert(['id'=>$id],$change);
    }
    
    public function update(int $id) {
        $fields = $this->storage->filter_storage('simple','class');
        if (empty($fields)) {
            return $id;
        }
        foreach ($this->storage->get_inheritance() as $inheritance) {
            if ($inheritance == "object") {
                $this->update_core($id);
            }
            $table = Classes::get_table_of_class($inheritance);
            if (isset($fields[$inheritance])) {
                $this->update_table($id,$table,$fields[$inheritance]);
            }
        }
        return $id;
    }
    
    public function delete(int $id) {
        foreach ($this->storage->get_inheritance() as $inheritance) {
            $table = Classes::get_table_of_class($inheritance);
            DB::table($table)->where('id',$id)->delete();
        }
        return $id;        
    }
    
    /**
     * Löscht die höhergestellten Tabellen 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storagestoragemodule_base::degrade()
     */
    public function degrade(int $id,array $degration_info) {
        DB::table('objects')->where('id',$id)->update(['classname'=>$degration_info['newclass']]);
        foreach ($degration_info['diff'] as $class) {
            DB::table(Classes::get_table_of_class($class))->where('id',$id)->delete();
        }
        return $id;
    }
}