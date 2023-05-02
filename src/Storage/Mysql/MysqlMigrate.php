<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;

class MysqlMigrate
{
    
    use ClassTables, ColumnInfo, PropertyUtils;
    
    protected $class_name;
    
    protected $basic_table_name;
    
    public function __construct(public $storage) {}

    public function doMigrate()
    {
        $this->class_name = $this->storage->getCaller()::getInfo('name');
        $this->basic_table_name = $this->storage->getCaller()::getInfo('table');
        $this->checkTable();
    }
    
    /**
     * First checks if the table already exists. If yes, It's not a fresh
     * migration, so the test is a little bit more complex. If no, we simply
     * create the new tables as required
     */
    protected function checkTable()
    {
        if (Schema::hasTable($this->basic_table_name)) {
            $this->checkBasicTableChange();
        } else {
            $this->createBasicTable();
        }
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
        Schema::table($this->basic_table_name, function ($table) {
            $this->addField($this->basic_table_name, $property->getName(), $property);
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
        $type = $property->getType();
        if ($type == $this->getColumnType($this->basic_table_name, $property->getName())) {
            return;
        }
        Schema::table($this->basic_table_name, function ($table) {
           $table->$type($property->getName())->change(); 
        });
    }
    
    protected function checkVarchar(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
    }
    
    protected function checkEnum(Property $property)
    {
        if ($this->checkTableFieldExists($property)) {
            return;
        }
    }
    
    protected function checkNewOrChangedColumns()
    {
        $properties = $this->getAllProperties($this->storage->getCaller(), true);       
        foreach ($properties as $name => $property) {
            switch ($property->getType()) {
                case 'integer':
                case 'float':
                case 'date':
                case 'datetime':
                case 'time':
                case 'object':
                    $this->checkSimpleField($property); break;
                case 'varchar':
                    $this->checkVarchar($property); break;
                case 'enum':
                    $this->checkEnum($property); break;
                case 'arrayofstrings':
                    $this->checkArray($property,'string'); break;
                case 'arrayofobjects':
                    $this->checkArray($property,'object'); break;
                case 'calculated':
                    $this->checkCalculated($property); break;
                case 'tags':
                    break;
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
    
    protected function createArrayOfStrings(string $field_name)
    {
        Schema::create($this->basic_table_name.'_array_'.strtolower($field_name), function ($table) {
           $table->integer('id');
           $table->string('target',100);
           $table->integer('index');
           $table->primary(['id','index']);
        });            
    }
    
    protected function createArrayOfObjects(string $field_name)
    {
        Schema::create($this->basic_table_name.'_array_'.strtolower($field_name), function ($table) {
            $table->integer('id');
            $table->string('target',100);
            $table->integer('index');
            $table->primary(['id','index']);
        });
    }
    
    protected function createCalculated(string $field_name)
    {
        Schema::create($this->basic_table_name.'_calc_'.strtolower($field_name), function ($table) {
            $table->integer('id');
            $table->string('value',100);
            $table->primary(['id']);
        });
    }
    
    protected function addField($table, string $field_name, Property $info)
    {
        if ($info->getClass() !== $this->storage->getCaller()::getInfo('name')) {
            return;
        }
        $type = strtolower($info->getType());
        switch ($type) {
           case 'integer':
               $field = $table->integer($field_name);
               break;
           case 'object':
               $field = $table->integer($field_name)->nullable()->default(null);
               break;               
           case 'varchar':
               $field = $table->string($field_name,$info->getMaxLen());
               break;
          case 'enum':
               $field = $table->enum($field_name, $info->getEnumValues());
               break;
          case 'date':
               $field = $table->date($field_name);
               break;
          case 'datetime':
              $field = $table->datetime($field_name);
              break;
          case 'time':
              $field = $table->time($field_name);
              break;
          case 'float':
              $field = $table->date($field_name);
              break;
          case 'text':
              $field = $table->text($field_name);
              break;
          case 'arrayofstrings':
              $this->createArrayOfStrings($field_name);
              return;
              break;
          case 'arrayofobjects':
              $this->createArrayOfObjects($field_name);
              return;
              break;
          case 'calculated':    
              $this->createCalculated($field_name);
              return;
              break;
          default:
              return;
        }
        if ($info->getSearchable()) {
            $field = $field->index($this->basic_table_name.'_'.$field_name);
        }
        
        if ($info->getDefaultsNull()) {
            $field->nullable()->default(null);
        } else if (!empty($info->getDefault())) {
            $field->default($info->getDefault());
        }
    }
    
    protected function createBasicTable()
    {
        Schema::create($this->basic_table_name, function ($table) {
            $table->integer('id');
            $table->primary('id');
            $simple = $this->storage->getCaller()->getProperties()->get();
            foreach ($simple as $field => $info) {
                $this->addField($table, $field, $info);
            }
        });
            
    }
    
}