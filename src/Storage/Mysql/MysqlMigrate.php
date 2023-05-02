<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;

class MysqlMigrate
{
    
    use ClassTables;
    
    protected $class_name;
    
    protected $basic_table_name;
    
    public function __construct(public $storage) {}

    public function doMigrate()
    {
        $this->class_name = $this->storage->getCaller()::getInfo('name');
        $this->basic_table_name = $this->storage->getCaller()::getInfo('table');
        $this->checkBasicTable();
    }
    
    protected function checkBasicTable()
    {
        if (Schema::hasTable($this->basic_table_name)) {
            $this->checkBasicTableChange();
        } else {
            $this->createBasicTable();
        }
    }
    
    protected function checkBasicTableChange()
    {
        
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