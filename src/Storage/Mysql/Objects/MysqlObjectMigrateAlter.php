<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;
use Sunhill\ORM\Storage\Mysql\Utils\ClassTables;
use Sunhill\ORM\Storage\Mysql\Utils\ColumnInfo;
use Sunhill\ORM\Storage\Mysql\MysqlAction;

class MysqlObjectMigrateAlter extends MysqlAction
{
    
    use ClassTables, ColumnInfo, PropertyUtils;
    
    public function __construct(public $storage) {}

    public function doMigrate()
    {
        $this->class_name = $this->storage->getCaller()::getInfo('name');
        $this->basic_table_name = $this->storage->getCaller()::getInfo('table');
        $this->checkBasicTableChange();
    }
    
    /**
     * Checks the main table for column that aren't defined by the class anymore
     * If such exists, droü them from the table
     */
    protected function checkForDroppedSimpleColumns()
    {
        $list = $this->getColumnNames($this->basic_table_name);
        array_shift($list); // delete column 'id'
        $class = Classes::getNamespaceOfClass($this->class_name);
        
        foreach ($list as $column) {
            if (!$class::getPropertyObject($column)) {
                Schema::dropColumns($this->basic_table_name,[$column]);
            }
        }
    }
    
    /**
     * Checks for array or map fields that aren't defined in the class anymore
     * If such exist, drop the tables from the database
     */
    protected function checkForDroppedOtherColumns()
    {
        $tables = $this->collectClassTables($this->class_name);
        
        $class = Classes::getNamespaceOfClass($this->class_name);
        foreach ($tables as $field => $table) {
            if (!$class::getPropertyObject($field)) {
                Schema::drop($table);
            }
        }
    }
    
    protected function checkForDroppedColumns()
    {
        $this->checkForDroppedSimpleColumns();
        $this->checkForDroppedOtherColumns();
    }
    
    protected function checkNewOrChangedColumns()
    {
        $properties = $this->storage->getCaller()::getPropertyDefinition();       
        $helper = new MysqlObjectAlterColumn();
        foreach ($properties as $name => $property) {
            $helper->handleProperty($property);
        }
    }
    
    protected function checkBasicTableChange()
    {
        $this->checkForDroppedColumns();
        
        // At this point the database doesn't contain any entries, that aren't 
        // defines by the class anymore. Now we just have to search for new or
        // changed columns
        
        $this->checkNewOrChangedColumns();
    }
        
}