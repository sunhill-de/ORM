<?php 
/**
 * @file StorageModuleMysqlSimple.php
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\ORM\Storage;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;

class StorageModuleMySQLSimple extends StorageModuleBase 
{
    
    private $sorted;
    
    public function load(int $id) 
    {
        foreach ($this->storage->getInheritance() as $inheritance) {
            $table = ($inheritance=='object')?'objects':Classes::getTableOfClass($inheritance); 
            $result = DB::table($table)->where('id','=',$id)->first();
            if (!empty($result)) {
                foreach ($result as $name => $value) {
                    $this->storage->$name = $value;
                }
            } else {
                throw new StorageException("There is no ID '$id' in the table '$table'.");
            }
        }  
        return $id;
    }
   
    public function prepare_insert(int $id) 
    {
            
    }
    
    /**
     * Method taken from https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     * Generates a v4 uuid string and returns it
     */
    private function getUUID(): String
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }


    private function store_core() 
    {
        $uuid = $this->getUUID();
        $this->storage->getCaller()->uuid = $uuid; // set uuid back to the object
        $fields = ['classname'=>Classes::getClassName($this->storage->getCaller()),
            'created_at'=>DB::raw('current_timestamp'),
            'updated_at'=>DB::raw('current_timestamp'),
            'uuid'=>$uuid
        ];
        DB::table('objects')->insert($fields);
        return DB::getPdo()->lastInsertId();        
    }
    
    private function store_table($id, $table, $fields)
    {    
        $fields['id'] = $id;
        DB::table($table)->insert($fields);
    }
    
    public function insert(int $id) 
    {
        $id = $this->store_core();
        $fields = $this->storage->filterStorage('simple','class');
        foreach ($this->storage->getInheritance() as $inheritance) {
            if ($inheritance == "object") {
                return $id;
            }
            $table = Classes::getTableOfClass($inheritance);
            if (!isset($fields[$inheritance])) {
                $this->store_table($id,$table,[]);
            } else {
                $this->store_table($id,$table,$fields[$inheritance]);
            }
        }
        return $id;
    }
    
    private function update_core(int $id) 
    {
        DB::table('objects')->where('id',$id)->update(['updated_at'=>DB::raw('current_timestamp')]);
    }
    
    private function update_table($id, $table, $fields) 
    {
        $change = array();
        foreach ($fields as $field=>$diff) {
            $change[$field] = $diff['TO'];
        }
        DB::table($table)->updateOrInsert(['id'=>$id],$change);
    }
    
    public function update(int $id) 
    {
        $fields = $this->storage->filterStorage('simple','class');
        if (empty($fields)) {
            return $id;
        }
        foreach ($this->storage->getInheritance() as $inheritance) {
            if ($inheritance == "object") {
                $this->update_core($id);
            }
            $table = Classes::getTableOfClass($inheritance);
            if (isset($fields[$inheritance])) {
                $this->update_table($id,$table,$fields[$inheritance]);
            }
        }
        return $id;
    }
    
    public function delete(int $id)
    {
        foreach ($this->storage->getInheritance() as $inheritance) {
            $table = Classes::getTableOfClass($inheritance);
            DB::table($table)->where('id',$id)->delete();
        }
        return $id;        
    }
    
    /**
     * Löscht die höhergestellten Tabellen 
     * {@inheritDoc}
     * @see \Sunhill\ORM\StorageStorageModuleBase::degrade()
     */
    public function degrade(int $id, array $degration_info)
    {
        DB::table('objects')->where('id',$id)->update(['classname'=>$degration_info['newclass']]);
        foreach ($degration_info['diff'] as $class) {
            DB::table(Classes::getTableOfClass($class))->where('id',$id)->delete();
        }
        return $id;
    }
}
