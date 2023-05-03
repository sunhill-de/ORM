<?php

/**
 * Some helpers for creating columns
 */
namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;

trait ColumnCreate
{
    
    protected $class_name;
    
    protected $basic_table_name;
    
    protected function getHelpTableName(string $type, string $field_name)
    {
        return $this->basic_table_name.'_'.$type.'_'.strtolower($field_name);    
    }
    
    protected function createArrayTable(string $field_name, string $type)
    {
        Schema::create($this->getHelpTableName('array', $field_name), function ($table) use ($type) {
            $table->integer('id');
            $table->$type('target');
            $table->integer('index');
            $table->primary(['id','index']);
        });            
    }
    
    protected function createArrayOfStrings(string $field_name)
    {
        $this->createArrayTable($field_name, 'string');
    }
    
    protected function createArrayOfObjects(string $field_name)
    {
        $this->createArrayTable($field_name, 'integer');
    }
    
    protected function createCalculated(string $field_name)
    {
        Schema::create($this->getHelpTableName('calc',$field_name), function ($table) {
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
        
}