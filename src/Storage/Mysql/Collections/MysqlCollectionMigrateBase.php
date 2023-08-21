<?php
/**
 * @file MysqlMigrateFresh
 * Helper that creates a fresh table of the given object or collection
 */
namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Traits\PropertyUtils;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Properties\Utils\DefaultNull;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Storage\Mysql\Utils\ColumnInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class MysqlCollectionMigrateBase extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers, ColumnInfo;
    
    protected function handleCommonProperties($field, $property)
    {
        if ($property->searchable) {
            $field->index();
        }
        if (!is_null($property->default)) {
            if (is_a($property->default, DefaultNull::class, true)) {
                $field->nullable()->default(null);
            } else {
                $field->default($property->default);
            }
        }
        if ($property->nullable) {
            $field->nullable();
        }
        return $field;
    }
    
    protected function createArrayOrMap($property, bool $is_map)
    {
        $table_name = $this->main_table_name.'_'.$property->name;
        
        Schema::create($table_name, function($table) use ($property, $is_map) {
            $table->integer('id')->primary();
            if ($is_map) {
                $table->string('index',50);
            } else {
                $table->integer('index');
            }
            switch ($property->element_type) {
                case PropertyInteger::class:
                case PropertyBoolean::class:
                case PropertyCollection::class:
                case PropertyObject::class:
                    $table->integer('value');
                    break;
                case PropertyVarchar::class:
                case PropertyEnum::class:
                    $table->string('value');
                    break;
                case PropertyDate::class:
                    $table->date('value');
                    break;
                case PropertyDatetime::class:
                    $table->datetime('value');
                    break;
                case PropertyTime::class:
                    $table->time('value');
                    break;
                case PropertyFloat::class:
                    $table->float('value');
                    break;
            }
        });
    }
    
    
}