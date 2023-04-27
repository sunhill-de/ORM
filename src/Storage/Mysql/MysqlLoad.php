<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;

/**
 * Helper class to load an object out of the database
 * @author klaus
 *
 */
class MysqlLoad
{
    
    public function __construct(public $storage) {}
        
    protected $additional_tables = [];
    
    public function doLoad(int $id)
    {
        $this->additional_tables = $this->collectAdditionalTables();
        $this->loadClassTables($id);
        $this->loadArrays($id);
        $this->loadTags($id);
        $this->loadAttributes($id);
        $this->loadCalculated($id);        
    }
    
    protected function collectClassTables(string $class)
    {
        $table = Classes::getTableOfClass($class);
        $search_name_array = $table.'_array_';
        $search_name_calc = $table.'_calc_';
        
        $all = Schema::getAllTables();
        $result = ['array'=>[],'calc'=>[]];
        foreach ($all as $table) {
            if (substr($table->name,0,strlen($search_name_array)) == $search_name_array) {
                $result['array'][substr($table->name,strlen($search_name_array))] = $table->name;                
            } else if (substr($table->name,0,strlen($search_name_calc)) == $search_name_calc) {
                $result['calc'][substr($table->name,strlen($search_name_calc))] = $table->name;
            }
        }
    
        return $result;
    }
    
    protected function collectAdditionalTables()
    {
        $result = ['array'=>[],'calc'=>[]];
        
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy);
        
        foreach ($hirarchy as $class) {
            $result = array_merge_recursive($result, $this->collectClassTables($class));
        }
        
        return $result;
    }
    
    /**
     * Loads all values from the standard tables including objects
     * @param int $id
     */
    private function loadClassTables(int $id)
    {
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy); // remove object
        
        $query = DB::table('objects');
        foreach ($hirarchy as $class) {
            $table = Classes::getTableOfClass($class);
            $query->join($table,'objects.id','=',$table.'.id');
        }
        $result = $query->where('objects.id',$id)->first();
        foreach ($result as $key => $value) {
            $this->storage->setEntity($key, $value);
        }
    }
    
    private function getArrayTables(int $id): array
    {
        return $this->additional_tables['array'];
    }
    
    private function loadArrays(int $id)
    {
        $array_table = $this->getArrayTables($id);
        foreach ($array_table as $field => $table) {
            $result = array_column(DB::table($table)->where('id',$id)->get()->toArray(),'target');
            $this->storage->setEntity($field, $result);
        }
    }
    
    private function loadTags(int $id)
    {
        $this->storage->setEntity('tags',array_column(DB::table('tagobjectassigns')->where('container_id',$id)->get()->toArray(),'tag_id'));
    }
    
    private function loadAttributes(int $id)
    {
        $result = [];
        $query = DB::table('attributevalues')->join('attributes','attributevalues.attribute_id','=','attributes.id')
        ->where('object_id',$id)->get()->toArray();
        foreach ($query as $attribute) {
            $entry = new \StdClass();
            $entry->name = $attribute->name;
            if ($entry->type = $attribute->type == 'text') {
                $entry->value = $attribute->textvalue;
            } else {
                $entry->value = $attribute->value;
            }
            $result[] = $entry;
        }
        $this->storage->setEntity('attributes',$result);
    }
    
    private function loadCalculated(int $id)
    {
        $calc_tables = $this->getCalcTables($id);
        foreach ($calc_tables as $field => $table) {
            $this->storage->setEntity($field,DB::table($table)->where('id',$id)->first()->value);
        }
            
    }

    private function getCalcTables(int $id): array
    {
        return $this->additional_tables['calc'];
    }
    
    
}