<?php

namespace Sunhill\ORM\Storage\Mysql;

use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\Schema;

trait ClassTables
{

    protected $additional_tables = [];
    
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
    
    protected function getArrayTables(): array
    {
        return $this->additional_tables['array'];
    }   
    
    protected function getCalcTables(): array
    {
        return $this->additional_tables['calc'];
    }
    
}