<?php

/**
 * Some helpers for managing multiple tables
 */
namespace Sunhill\ORM\Storage\Mysql\Utils;

use Sunhill\ORM\Facades\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait TableManagement
{

    /**
     * An array of the fields to add/update to the database
     * @var array
     */
    protected $tables = [];
    
    /**
     * The currect working id
     * @var integer
     */
    protected $id = 0;
    
    /**
     * Writes the $fields to the $table
     * @param string $table The name of the table to write to
     * @param array $fields An associative array with the fields of the table
     */
    protected function storeTable(string $table, array $fields)
    {
        DB::table($table)->insert($fields);
    }
    
    /**
     * Update the $fields in the $table
     * @param string $table
     * @param array $fields
     * @param string $id_field The name of the id field (default 'id')
     */
    protected function updateTable(string $table, array $fields, string $id_field = 'id')
    {
        DB::table($table)->where($id_field, $this->id)->update($fields);
    }
    
    protected function addEntry(string $table, string $key, $value)
    {
        if (isset($this->tables[$table])) {
            $this->tables[$table][$key] = $value;
        } else {
            $this->tables[$table] = [$key => $value];
        }
    }
    
    protected function addEntryRecord(string $table, array $data)
    {
        if (isset($this->tables[$table])) {
            $this->tables[$table][] = $data;
        } else {
            $this->tables[$table] = [$data];
        }
    }
    
    protected function getIDField(string $table)
    {
        switch ($table) {
            case 'tagobjectassigns':
                return 'container_id';
            case 'attributeobjectassigns':
                return 'object_id';
        }
        if (substr($table,0,5) == 'attr_') {
            return 'object_id';
        }
        return 'id';
    }
    
    protected function appendID(string $table, array &$fields)
    {
        if (isset($fields[0]) && is_array($fields[0])) {
            $this->addIDToAllRecords($fields, $this->getIDField($table));
        } else {
            $fields[$this->getIDField($table)] = $this->id;
        }
    }
        
}