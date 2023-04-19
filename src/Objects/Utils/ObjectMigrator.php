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
            $this->insertPropertyType($result, $property);
            $this->insertPropertyDefault($result, $property);
            $this->insertPropertyIndex($result, $property);
            $this->insertPropertyAdditional($result, $property);
        }
        return $result;
    }
    
    protected function insertPropertyType(&$result, $property)
    {
        $name = $property->getName();
        $result[$name] = ['type'=>$property->getType()];
    }
    
    protected function insertPropertyDefault(&$result, $property)
    {
        $name = $property->getName();
        if ($property->getDefaultsNull()) {
            $result[$name]['default'] = 'null';
        } else {
            $default = $property->getDefault();
            if (!is_null($default)) {
                $result[$name]['default'] = $default;
            }
        }
    }
    
    protected function insertPropertyIndex(&$result, $property)
    {
        if ($property->getSearchable()) {
            $result[$property->getName()]['searchable'] = true;
        }
    }
    
    protected function insertPropertyAdditional(&$result, $property)
    {
        switch ($property->getType()) {
            case 'varchar':
                $result[$property->getName()]['maxlen'] = $property->getMaxLen();
                break;
            case 'enum':
                $result[$property->getName()]['enum'] = $property->getEnumValues();
                break;
        }
    }
    
    /**
     * Adjusts the names of properties and database entries
     * @param string $type
     * @return string
     */
    protected function mapType($table, string $table_name, string $field_name, array $info)
    {
        $type = strtolower($info['type']);
        switch ($type) {
            case 'integer':
                $field = $table->integer($field_name); 
                break;
            case 'varchar':
                $field = $table->string($field_name,isset($info['maxlen'])?$info['maxlen']:'255');
                break;
            case 'enum':
                $field = $table->enum($field_name, $info['enum']);
                break;
            default:
                $field = $table->$type($field_name);
        }
        if (isset($info['searchable'])) {
            $field = $field->index($table_name.'_'.$field_name);
        }
        if (isset($info['default'])) {
            if ($info['default'] == 'null') {
                $field = $field->nullable()->default(null);
            } else {
                $field = $field->default($info['default']);
            }
        }
    }
    
    /**
     * creates a new table with the current properties
     */
    private function createTable()
    {
        Schema::create($this->class_tablename, function ($table) {
            $table->integer('id');
            $simple = $this->getCurrentProperties();
            foreach ($simple as $field => $info) {
                $this->mapType($table, $this->class_tablename, $field, $info);
            }
        });
    }

    protected function fixTable()
    {
        $current_definition = $this->getCurrentProperties();
        $current_database = $this->getDatabaseProperties();
        unset($current_database['id']);
        $this->dropRemoved($current_definition, $current_database);
        $this->alterColumns($current_definition, $current_database);
    /*
            // If the table already exsists, check if we have to change it
        $current = $this->getCurrentProperties();
        $database = $this->getDatabaseProperties();
        
        $removed = $this->removeColumns($current,$database);
        $added = $this->addColumns($current,$database);
        
        //     $altered = $this->alterColums($current,$database);
        $this->postMigration($added,$removed,[]); */
    }
    
    protected function getDatabaseProperties()
    {
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        return DB::getDoctrineSchemaManager()->listTableColumns($this->class_tablename);    
    }
    
    /**
     * Drops all database table columns that aren't defined anymore
     * @param unknown $current_definition
     * @param unknown $current_database
     */
    protected function dropRemoved($current_definition, $current_database)
    {
        foreach ($current_database as $field => $info) {
            if (!isset($current_definition[$field])) {
                $this->dropColumn($field);       
            }
        }
    }
    
    /**
     * Drops the given column
     * @param string $column
     */
    protected function dropColumn(string $column)
    {
        Schema::table($this->class_tablename, function($table) use ($column) {
            $table->dropColumn($column); 
        });
    }
    
    protected function addColumn($field, $info)
    {
        Schema::table($this->class_tablename, function($table) use ($field, $info) {
            $this->mapType($table, $this->class_tablename, $field, $info); 
        });
    }
    
    protected function testForDifferences(string $field, array $current_field, $db_field)
    {
        
    }
    
    protected function alterColumns($current_definition, $current_database) 
    {
        foreach ($current_definition as $field => $info) {
            if (!isset($current_database[$field])) {
                $this->addColumn($field, $info);
                continue;
            }
            $this->testForDifferences($field, $info, $current_database[$field]);
        }
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
