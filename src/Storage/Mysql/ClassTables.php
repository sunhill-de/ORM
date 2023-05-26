<?php

/**
 * Some helpers for collecting specialized tables (like array or calculated)
 */
namespace Sunhill\ORM\Storage\Mysql;

use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\Schema;

trait ClassTables
{

    protected $additional_tables = [];
    
    protected function collectClassTables(string $class)
    {
        $table = Classes::getTableOfClass($class);
        $search_name = $table.'_';
        
        $all = Schema::getAllTables();
        $result = [];
        foreach ($all as $table) {
            if (substr($table->name,0,strlen($search_name)) == $search_name) {
                $result[substr($table->name,strlen($search_name))] = $table->name;
            } 
        }        
        return $result;
    }
    
    protected function collectAdditionalTables()
    {
        $result = [];
        
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy);
        
        foreach ($hirarchy as $class) {
            $result = array_merge($result, $this->collectClassTables($class));
        }
        
        return $result;
    }

    protected function getAdditionalTables(): array
    {
        return $this->additional_tables;
    }
    
}