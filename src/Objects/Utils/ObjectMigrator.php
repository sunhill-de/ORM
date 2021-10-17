<?php

/**
 * @file ObjectMigrator.php
 * Provides the ObjectMigrator class that is a supporting class for the class manager
 * Lang en
 * Reviewstatus: 2021-10-06
 * Localization: none
 * Documentation: incomplete
 * Tests: Feature/Objects/Utils/ObjectMigrateTest.php
 * Coverage: unknown
 * Dependencies: Classes
 * PSR-State: Type-Hints missing
 */
namespace Sunhill\ORM\Objects\Utils;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;

class object_migrator 
{
 
    private $class_name = '';
    
    private $class_namespace = '';
    
    private $class_tablename = '';
    
    /**
     * This method is called by the class manager to check if the passed class properties are on the
     * same level as the database properties. If not it fixed this difference to adjust the database
     * to this class.
     * @param $class_name string: The (internal) name of this class
     */
    public function migrate(string $class_name)
    {
        if ($class_name == 'object') {
            return; // Dont migrate object because its done with migrate:fresh
        }
        $this->class_name = $class_name;
        $this->class_namespace = Classes::getNamespaceOfClass($class_name);
        $this->class_tablename = Classes::getTableOfClass($class_name);
        
        // Initialize the properties otherwise we can't access them
        $this->class_namespace::initializeProperties();
            
        if ($this->tableExists()) {
            // If the table already exsists, check if we have to change it
            $current = $this->getCurrentProperties();
            $database = $this->getDatabaseProperties();
            $removed = $this->removeColumns($current,$database);
            $added = $this->addColumns($current,$database);
            $altered = $this->alterColums($current,$database);
            $this->postMigration($added,$removed,$altered);
        } else {
            // If the table doesn't exists, create it
            $this->create_table();
        }
    }
    
    /**
     * Check if the table of this object exists at all
     * @return boolean true, of the table exists otherwise false
     */
    private function tableExists(): bool
    {
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
    private function mapType(array $info): string
    {
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
    private function create_table()
    {
        $statement = 'create table '.$this->class_tablename.' (id int primary key';
        $simple = $this->getCurrentProperties();
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
    private function getCurrentProperties() 
    {
        $properties = $this->class_namespace::staticGetPropertiesWithFeature('simple','class');
        
        $result = array();
        if (!isset($properties[$this->class_name])) {
            return $result;
        }
        
        foreach ($properties[$this->class_name] as $property) {
            $result[$property->getName()] = ['type'=>$property->getType()];
            switch ($property->getType()) {
                case 'varchar':
                    $result[$property->getName()]['maxlen'] = $property->getMaxLen();
                    break;
                case 'enum':
                    $first = true;
                    $resultstr = '';
                    foreach ($property->getEnumValues() as $value) {
                        if (!$first) {
                            $resultstr .= ',';
                        }
                        $resultstr .= "'$value'";
                        $first = false;
                    }
                    $result[$property->getName()]['enum'] = $resultstr;
                    break;
            }
        }
        return $result;
    }
    
    /**
     * Return the current properties of the database
     * @return NULL[][]
     */
    private function getDatabaseProperties() 
    {
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
    private function removeColumns($current,$database) 
    {
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
    private function addColumns($current,$database) 
    {
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
    private function alterColums($current,$database) 
    {
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
    private function postMigration(array $added, array $deleted, array $changed) 
    {
        
    }
}
