<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyText;
use DeepCopy\Exception\PropertyException;


class MysqlMigrateAlter
{
    
    use ClassTables, ColumnInfo, PropertyUtils, ColumnCreate;
    
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
     * Checks a additional array or calc table for unneeded tables
     */
    private function checkTables(array $tables, string $type)
    {
        $class = Classes::getNamespaceOfClass($this->class_name);
        foreach ($tables as $table) {
            list($dummy,$column) = explode('_'.$type.'_',$table);
            if (!$class::getPropertyObject($column)) {
                Schema::drop($table);
            }
        }        
    }
    
    protected function checkForDroppedOtherColumns()
    {
        $tables = $this->collectClassTables($this->class_name);
        
        $this->checkTables($tables['array'],'array');
        $this->checkTables($tables['calc'],'calc');
    }
    
    protected function addTableField(Property $property)
    {
        Schema::table($this->basic_table_name, function ($table) use ($property) {
            $default = $property->getDefault();
            if ($property->getDefaultsNull() || is_null($default)) {
                $property->setDefault(null); // Later columns need a default value
            }
            $this->addField($table, $property->getName(), $property);
        });
    }
    
    protected function checkTableFieldExists(Property $property)
    {
        if (!$this->columnExists($this->basic_table_name, $property->getName())) {
            $this->addTableField($property);
            return true;
        }
        return false;
    }
    
    protected function checkSimpleField(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
        $class_type = $property->getType();
        $db_type = $this->getColumnType($this->basic_table_name, $property->getName());
        if ($class_type !== $db_type) {
            Schema::table($this->basic_table_name, function ($table) use ($class_type, $property){
                $field = $table->$class_type($property->getName());
                if ($property->getDefaultsNull()) {
                    $field->nullable()->default(null);
                } else if (!empty($property->getDefault())) {
                    $field->default($property->getDefault());
                }
                $field->change();
            });
            return;
        }
        if ($property->getDefaultsNull()) {
            if (!$this->getColumnDefaultsNull($this->basic_table_name, $property->getName())) {
                Schema::table($this->basic_table_name, function ($table) use ($property, $class_type) {
                   $table->$class_type($property->getName())->nullable()->default(null)->change(); 
                });
            }
            return;
        }
        if (!is_null($property->getDefault())) {
            if ($this->getColumnDefault($this->basic_table_name, $property->getName()) !== $property->getDefault()) {
                Schema::table($this->basic_table_name, function ($table) use ($property, $class_type) {
                    $table->$class_type($property->getName())->default($property->getDefault())->change();
                });                    
            }
        }
    }
    
    protected function checkObjectField(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
        $db_type = $this->getColumnType($this->basic_table_name, $property->getName());
        if ('integer' !== $db_type) {
            Schema::table($this->basic_table_name, function ($table) use ($property){
                $table->integer($property->getName())->change();
            });
        }
    }
    
    protected function checkVarchar(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
        if ('string' !== $this->getColumnType($this->basic_table_name, $property->getName())) {
            Schema::table($this->basic_table_name, function ($table) use ($property) {
                $table->string($property->getName(), $property->getMaxLength())->change();                
            });
        }
        if ($property->getDefaultsNull()) {
            if (!$this->getColumnDefaultsNull($this->basic_table_name, $property->getName())) {
                Schema::table($this->basic_table_name, function ($table) use ($property) {
                    $table->string($property->getName(),$property->getMaxLength())->nullable()->default(null)->change();
                });
            }
            return;
        }
        if (!is_null($property->getDefault())) {
            if ($this->getColumnDefault($this->basic_table_name, $property->getName()) !== $property->getDefault()) {
                Schema::table($this->basic_table_name, function ($table) use ($property) {
                    $table->string($property->getName(),$property->getMaxLength())->default($property->getDefault())->change();
                });
            }
        }
    }
    
    protected function checkEnum(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
    }
    
    protected function addArrayTable(string $table_name, string $type)
    {
        Schema::create($table_name, function($table) use ($type) {
           $table->integer('id')->primary();
           switch ($type) {
               case PropertyInteger::class:
               case PropertyObject::class:
                   $table->integer('value'); break;
               case PropertyVarchar::class:
               case PropertyEnum::class:
                   $table->string('value'); break;
               case PropertyDate::class:
                   $table->date('value'); break;
               case PropertyDateTime::class:
                   $table->datetime('value'); break;
               case PropertyTime::class:
                   $table->time('value'); break;
               case PropertyFloat::class:
                   $table->float('value'); break;
               default:
                   throw new PropertyException("Can't build an array of ".$type);
           }
           $table->integer('index');
        });
    }
    
    protected function checkArray(Property $property)
    {
        $table_name = $this->basic_table_name.'_array_'.$property->getName();
        if (!$this->tableExists($table_name)) {
            $this->addArrayTable($table_name, $property->getElementType());
            return;
        }
    }
    
    protected function addCalcTable(string $table_name)
    {
        Schema::create($table_name, function($table) {
            $table->integer('id')->primary();
            $table->string('value');
        });            
    }
    
    protected function checkCalculated(Property $property)
    {
        $table_name = $this->basic_table_name.'_calc_'.$property->getName();
        if (!$this->tableExists($table_name)) {
            $this->addCalcTable($table_name);
            return;
        }
    }
    
    protected function checkNewOrChangedColumns()
    {
        $properties = $this->storage->getCaller()::getPropertyDefinition();       
        foreach ($properties as $name => $property) {
            switch ($property::class) {
                case PropertyInteger::class:
                case PropertyFloat::class:
                case PropertyDate::class:
                case PropertyDatetime::class:
                case PropertyTime::class:
                case PropertyText::class:
                    $this->checkSimpleField($property); break;
                case PropertyObject::class:
                    $this->checkObjectField($property); break;
                case PropertyVarchar::class:
                    $this->checkVarchar($property); break;
                case PropertyEnum::class:
                    $this->checkEnum($property); break;
                case PropertyArray::class:
                    $this->checkArray($property); break;
                case PropertyCalculated::class:
                    $this->checkCalculated($property); break;
                case 'tags':
                    break;
                default:
                    throw new PropertyException("Unknown property type ".$property::class);
            }
        }
    }
    
    protected function checkBasicTableChange()
    {
        $this->checkForDroppedSimpleColumns();
        $this->checkForDroppedOtherColumns();
        
        // At this point the database doesn't contain any entries, that aren't 
        // defines by the class anymore. Now we just have to search for new or
        // changed columns
        
        $this->checkNewOrChangedColumns();
    }
        
}