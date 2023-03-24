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
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Facades\Classes;

class ObjectMigrator 
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
        
        $this->storeInformations($class_name);
        $this->migrateTable();
    }
    
    /**
     * Stores the important information for later work
     * 
     * @param string $class_name
     * 
     * Test: tests/Unit/Objects/Utils/ObjectMigratorTest::testStoreInformations
     */
    protected function storeInformations(string $class_name)
    {
        $this->class_name = $class_name;
        $this->class_namespace = Classes::getNamespaceOfClass($class_name);
        $this->class_tablename = Classes::getTableOfClass($class_name);
        
        // Initialize the properties otherwise we can't access them
        $this->class_namespace::initializeProperties();        
    }
    
    /**
     * If the table already exists, check if we have to change it otherwise
     * create the table from scratch
     * 
     * Test: tests/Unit/Objects/Utils/ObjectMigratorTest::testMigrateTable
     */
    protected function migrateTable()
    {
        if ($this->tableExists()) {
            $this->fixTable();
        } else {
            // If the table doesn't exists, create it
            $this->createTable();
        }        
    }

    protected function fixTable()
    {
        // If the table already exsists, check if we have to change it
        $current = $this->getCurrentProperties();
        $database = $this->getDatabaseProperties();
        $removed = $this->removeColumns($current,$database);
        $added = $this->addColumns($current,$database);
        $altered = $this->alterColums($current,$database);
        $this->postMigration($added,$removed,$altered);        
    }
    
    /**
     * Check if the table of this object exists at all
     * @return boolean true, of the table exists otherwise false
     * 
     * Test: tests/Unit/Objects/Utils/ObjectMigratorTest::testTableExists
     */
    protected function tableExists(): bool
    {
        return Schema::hasTable($this->class_tablename);
    }
    
    /**
     * Adjusts the names of properties and database entries
     * @param string $type
     * @return string
     */
    private function mapType(array $info): string
    {
        switch ($info['type']) {
            case 'Integer':
                return 'int(11)'; break;
            case 'Varchar':
                return 'varchar('.(isset($info['maxlen'])?$info['maxlen']:'255').')'; break;
            case 'Enum':
                return 'enum('.$info['enum'].')'; break;
            default:
                return strtolower($info['type']);
        }
    }
    
    /**
     * creates a new table with the current properties
     */
    private function createTable()
    {
        $statement = 'create table '.$this->class_tablename.' (id int primary key';
        $simple = $this->getCurrentProperties();
        foreach ($simple as $field => $info) {
            $statement .= ','.$field.' '.$this->mapType($info);
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
                case 'Varchar':
                    $result[$property->getName()]['maxlen'] = $property->getMaxLen();
                    break;
                case 'Enum':
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
        $fields = Schema::getColumnListing($this->class_tablename);
        $result = array();
        foreach ($fields as $field) {
            $result[$field] = [
                'type'=>DB::connection()->getDoctrineColumn($this->class_tablename, $field)->getType(),
                'null'=>true];
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
                $statement .= $this->mapType($info);
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
                $type = self::mapType($info);
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
