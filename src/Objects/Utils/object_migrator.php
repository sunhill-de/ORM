<?php

/**
 * @file object_migrator.php
 * Provides the object_migrator class that is a supporting class for the class manager
 * Lang en
 * Reviewstatus: 2020-10-22
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Classes
 */
namespace Sunhill\ORM\Objects\Utils;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;

class object_migrator {
 
    private $class_name = '';
    
    private $class_namespace = '';
    
    private $class_tablename = '';
    
    /**
     * This method is called by the class manager to check if the passed class properties are on the
     * same level as the database properties. If not it fixed this difference to adjust the database
     * to this class.
     * @param $class_name string: The (internal) name of this class
     */
    public function migrate(string $class_name) {
        $this->class_name = $class_name;
        $this->class_namespace = Classes::get_namespace_of_class($class_name);
        $this->class_tablename = Classes::get_table_of_class($class_name);
        
        // Initialize the properties otherwise we can't access them
        $this->class_namespace::initialize_properties();
            
        if ($this->table_exists()) {
            // If the table already exsists, check if we have to change it
            $current = $this->get_current_properties();
            $database = $this->get_database_properties();
            $removed = $this->remove_columns($current,$database);
            $added = $this->add_columns($current,$database);
            $altered = $this->alter_colums($current,$database);
            $this->post_migration($added,$removed,$altered);
        } else {
            // If the table doesn't exists, create it
            $this->create_table();
        }
    }
    
    /**
     * Check if the table of this object exists at all
     * @return boolean true, of the table exists otherwise false
     */
    private function table_exists() {
        $tables = DB::select(DB::raw("SHOW TABLES LIKE '".$this->class_tablename."'"));
        foreach ($tables as $name => $table) {
            foreach ($table as $field) {
                if ($field == $this->class_tablename) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Adjusts the names of properties and database entries
     * @param string $type
     * @return string
     */
    private function map_type($info) {
        switch ($info['type']) {
            case 'integer':
                return 'int(11)'; break;
            case 'varchar':
                return 'varchar('.$info['maxlen'].')'; break;
            case 'enum':
                return 'enum('.$info['enum'].')'; break;
            default:
                return $info['type'];
        }
    }
    
    /**
     * creates a new table with the current properties
     */
    private function create_table() {
        $statement = 'create table '.$this->class_tablename.' (id int primary key';
        $simple = $this->get_current_properties();
        foreach ($simple as $field => $info) {
            $statement .= ','.$field.' '.self::map_type($info);
        }
        $statement .= ')';
        DB::statement($statement);
    }
    
    /**
     * Returns the current properties of the class
     * @return array|string|NULL[][]
     */
    private function get_current_properties() {
        $properties = $this->class_namespace::static_get_properties_with_feature('simple','class');
        $result = array();
        if (!isset($properties[$this->class_name])) {
            return $result;
        }
        foreach ($properties[$this->class_name] as $property) {
            $result[$property->get_name()] = ['type'=>$property->get_type()];
            switch ($property->get_type()) {
                case 'varchar':
                    $result[$property->get_name()]['maxlen'] = $property->get_maxlen();
                    break;
                case 'enum':
                    $first = true;
                    $resultstr = '';
                    foreach ($property->get_enum_values() as $value) {
                        if (!$first) {
                            $resultstr .= ',';
                        }
                        $resultstr .= "'$value'";
                        $first = false;
                    }
                    $result[$property->get_name()]['enum'] = $resultstr;
                    break;
            }
        }
        return $result;
    }
    
    /**
     * Return the current properties of the database
     * @return NULL[][]
     */
    private function get_database_properties() {
        $fields = DB::select(DB::raw("SHOW COLUMNS FROM ".$this->class_tablename));
        $result = array();
        foreach ($fields as $field) {
            $result[$field->Field] = ['type'=>$field->Type,'null'=>$field->Null];
        }
        return $result;
    }
    
    /**
     * Removes columns that aren't defined by the class anymore
     * @param unknown $current
     * @param unknown $database
     * @param array of string The name of the columns that were removed
     */
    private function remove_columns($current,$database) {
        $result = [];
        foreach ($database as $name => $info) {
            if (!array_key_exists($name,$current) && ($name !== 'id')) {
                DB::statement("alter table ".$this->class_tablename." drop column ".$name);
                $result[] = $name;
            }
        }
        return $result;
    }
    
    /**
     * Add colums that are newly defined by the class
     * @param unknown $current
     * @param unknown $database
     * @param array of string The name of the columns that were added
     */
    private function add_columns($current,$database) {
        $result = [];
        foreach ($current as $name => $info) {
            if (!array_key_exists($name,$database)) {
                $statement = 'alter table '.$this->class_tablename." add column ".$name." ";
                $statement .= $this->map_type($info);
                DB::statement($statement);
                $result[] = $name;
            }
        }
        return $result;
    }
    
    /**
     * Change colums that are different in database and in class
     * @param unknown $current
     * @param unknown $database
     * @param array of string The name of the columns that were changed
     */
    private function alter_colums($current,$database) {
        $result = [];
        foreach ($current as $name => $info) {
            if (array_key_exists($name,$database)) {
                $type = self::map_type($info);
                if ($type !== $database[$name]['type']) {
                    $statement = 'alter table '.$this->class_tablename.' change column '.$name.' '.$name.' '.$type;
                    DB::statement($statement);
                    $result[] = $name;
                }
            }
        }
        return $result;
    }
    
    /**
     * Is called after a alteration of the database. The routine calls the routine post_migration of
     * all objects of the given class to inform the class, that the database was changed. In the post_migration
     * routine of the object an ajustment of data could be done. 
     * @param array $added Name of the fields that were added
     * @param array $deleted Name of the fields that were removed
     * @param array $changed Name of the fields that were changed
     */
    private function post_migration(array $added,array $deleted,array $changed) {
        
    }
}
