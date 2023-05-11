<?php

/**
 * Some helpers for creating columns
 */
namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyException;

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
            $table->$type('value');
            $table->integer('index');
            $table->primary(['id','index']);
        });            
    }
    
    protected function createArray(string $field_name, Property $info)
    {
        if (!is_a($info, PropertyArray::class)) {
            throw new PropertyException("The field '$field_name' is not an array");
        }
        switch ($info->getElementType()) {
            case PropertyInteger::class:
                $type = 'integer';
                break;
            case PropertyVarchar::class:
                $type = 'string';
                break;
            case PropertyTime::class:
                $type = 'time';
                break;
            case PropertyText::class:
                $type = 'text';
                break;
            case PropertyObject::class:
                $type = 'integer';
                break;
            case PropertyFloat::class:
                $type = 'float';                
                break;
            case Property::class:
                $type = 'datetime';
                break;
            case PropertyDate::class:
                $type = 'date';
                break;
            case PropertyBoolean::class:
                $type = 'bool';
                break;
            case PropertyEnum::class:
                $type = 'string';
                break;
            default:
                throw new PropertyException("It's not possible to build a array of ".$info::class);
        }
        $this->createArrayTable($field_name, $type);
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
        $type = strtolower($info->getType());
        switch ($type) {
            case 'integer':
                $field = $table->integer($field_name);
                break;
            case 'object':
                $field = $table->integer($field_name)->nullable()->default(null);
                break;
            case 'varchar':
            case 'string':
                $field = $table->string($field_name,$info->getMaxLen());
                break;
            case 'enum':
                $field = $table->string($field_name);
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
            case 'array':
                $this->createArray($field_name, $info);
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