<?php

/**
 * Some helpers for dealing with database tables and their columns (just a wrapper
 * around Schema)
 */
namespace Sunhill\ORM\Storage\Mysql\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait ColumnInfo
{
    
    /**
     * Returns true if the given table exists in this database
     *
     * @param string $table
     * @return bool
     * 
     * Test: \ColumnInfoTest
     */
    protected function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }
    
    /**
     * Returns an array of strings with the name of all defined columns of the given table
     * 
     * @param string $table
     * @return array
     * 
     * Test: \ColumnInfoTest
     */
    protected function getColumnNames(string $table): array
    {
        return Schema::getColumnListing($table);
    }
    
    /**
     * Returns true, if the given table has a column with the given name
     * 
     * @param string $table
     * @param string $column
     * @return bool
     * 
     * Test: \ColumnInfoTest
     */
    protected function columnExists(string $table, string $column): bool
    {
        return in_array($column, $this->getColumnNames($table));
    }
    
    /**
     * Returns the type of the given column in the given table
     * 
     * @param string $table
     * @param string $column
     * @return string
     * 
     * Test: \ColumnInfoTest
     */
    protected function getColumnType(string $table, string $column): string
    {
        return DB::getSchemaBuilder()->getColumnType($table, $column);
    }
    
    protected function getColumnDefault(string $table, string $column)
    {
        $column = Schema::getConnection()->getDoctrineColumn($table, $column);
        return $column->getDefault();
    }
    
    protected function getColumnDefaultsNull(string $table, string $column): bool
    {
        $column = Schema::getConnection()->getDoctrineColumn($table, $column);
        return ($column->getDefault() == null) && (!$column->getNotnull());        
    }
    
    protected function getColumnLength(string $table, string $column): int
    {
        $column_info = Schema::getConnection()->getDoctrineColumn($table, $column);
        if ($this->getColumnType($table,$column) == 'string') {
            return $column_info->getPrecision();
        }
        return 0;
    }
    
    protected function getEnumValue(string $table, string $column): array
    {
        $column_info = Schema::getConnection()->getDoctrineColumn($table, $column);
        if ($this->getColumnType($table,$column) == 'enum') {
            return $column_info->getEnum();
        }
        return [];        
    }
    
    protected function getColumnNullable(string $table, string $column): bool
    {
        $column = Schema::getConnection()->getDoctrineColumn($table, $column);
        return !$column->getNotnull();
        
    }
}