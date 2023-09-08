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

class MysqlCollectionMigrateAlter extends MysqlCollectionMigrateBase
{
    
    use PropertyHelpers, ColumnInfo;
    
    protected $table;
    
    protected $main_table_name;
    
    public function run()
    {
        if (($this->main_table_name = ($this->collection)::getInfo('table')) == 'objects') {
            return;
        }
        
        $this->searchDroppedSimpleColumns();
        $this->searchDroppedArrayColumns();
        $this->runProperties();
    }

    /**
     * Scans the main table for columns that aren't in the collection anymore
     */
    protected function searchDroppedSimpleColumns()
    {
        $columns = $this->getColumnNames($this->main_table_name);
        foreach ($columns as $column) {
            if (!($column == 'id') && !$this->collection::definesProperty($column)) {
                Schema::table($this->main_table_name, function($table) use ($column) {
                    $table->dropColumn($column);
                }); 
            }
        }
    }

    /**
     * Scans the database for tables that point to array fields that do not exist anymore
     */
    protected function searchDroppedArrayColumns()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tables as $table) {
            if (Str::startsWith($table,$this->main_table_name.'_')) {
                list($main,$sub) = explode('_',$table);
                if (!$this->collection::definesProperty($sub)) {
                    Schema::drop($table);                    
                }
            }
        }
    }
    
    protected function checkSimpleField($property, $type, $additional = null)
    {
        if (!$this->columnExists($this->main_table_name,$property->name)) {
            Schema::table($this->main_table_name, function($table) use ($property, $type, $additional) {
                if ($additional) {
                    $this->handleCommonProperties($table->$type($property->name, $additional), $property);
                } else {
                    $this->handleCommonProperties($table->$type($property->name), $property);                    
                }
            });
            return true;
        }
        
        // The field already exists, check if it changed by type
        if ($this->getColumnType($this->main_table_name, $property->name) != $type) {
            Schema::table($this->main_table_name, function($table) use ($property, $type, $additional) {
                if ($additional) {
                    $this->handleCommonProperties($table->$type($property->name, $additional), $property)->change();
                } else {
                    $this->handleCommonProperties($table->$type($property->name), $property)->change();
                }
            });                
            return true;
        }
        
        if (is_a($property->default, DefaultNull::class, true)) {
            if (!$this->getColumnDefaultsNull($this->main_table_name, $property->name)) {
                Schema::table($this->main_table_name, function($table) use ($type, $property) {
                   $table->$type($property->name)->nullable()->default(null)->change();
                });
            }
        } else if (!is_null($property->default)) {
            Schema::table($this->main_table_name, function($table) use ($type, $property) {
                $table->$type($property->name)->default($property->default)->change();
            });                
        }
        
        if ($property->nullable != $this->getColumnNullable($this->main_table_name, $property->name)) {
            Schema::table($this->main_table_name, function($table) use ($type, $property) {
                $table->$type($property->name)->nullable()->change();
            });                
        }
        // Type wasn't changed but other properties like default, nullable, maxlength could have
        return false;
    }
    
    public function handlePropertyArray($property)
    {
        if (!$this->tableExists($this->main_table_name.'_'.$property->name)) {
            $this->createArrayOrMap($property, false);
        } 
    }
    
    public function handlePropertyBoolean($property)
    {
        if ($this->checkSimpleField($property, 'integer')) {
            return;
        }
    }
    
    public function handlePropertyCalculated($property)
    {
        if ($this->checkSimpleField($property, 'string', 200)) {
            return;
        }
    }
    
    public function handlePropertyCollection($property)
    {
        if ($this->checkSimpleField($property, 'integer')) {
            return;
        }
    }
    
    public function handlePropertyDate($property)
    {
        if ($this->checkSimpleField($property, 'date')) {
            return;
        }
    }
    
    public function handlePropertyDateTime($property)
    {
        if ($this->checkSimpleField($property, 'datetime')) {
            return;
        }
    }
    
    public function handlePropertyEnum($property)
    {
        if ($this->checkSimpleField($property, 'string', 100)) {
            return;
        }
    }
    
    public function handlePropertyExternalReference($property)
    {
        if ($this->checkSimpleField($property, 'integer')) {
            return;
        }
    }
    
    public function handlePropertyFloat($property)
    {
        if ($this->checkSimpleField($property, 'float')) {
            return;
        }
    }
    
    public function handlePropertyInformation($property)
    {
        if ($this->checkSimpleField($property, 'string', 100)) {
            return;
        }
    }
    
    public function handlePropertyInteger($property)
    {
        if ($this->checkSimpleField($property, 'integer')) {
            return;
        }
    }
    
    public function handlePropertyKeyfield($property)
    {
        
    }
    
    public function handlePropertyMap($property)
    {
        if (!$this->tableExists($this->main_table_name.'_'.$property->name)) {
            $this->createArrayOrMap($property, false);
        }
    }
    
    public function handlePropertyObject($property)
    {
        if ($this->checkSimpleField($property, 'integer')) {
            return;
        }
    }
    
    public function handlePropertyTags($property)
    {
    }
    
    public function handlePropertyText($property)
    {
        if ($this->checkSimpleField($property, 'text')) {
            return;
        }
    }
    
    public function handlePropertyTime($property)
    {
        if ($this->checkSimpleField($property, 'time')) {
            return;
        }
    }
    
    public function handlePropertyVarchar($property)
    {
        if ($this->checkSimpleField($property, 'string', $property->max_len)) {
            return;
        }
        if ($property->max_len <> $this->getColumnLength($this->main_table_name,$property->name)) {
            Schema::table($this->main_table_name, function($table) use ($property) {
               $table->string($property->name, $property->max_len)->change(); 
            });
        }
    }
}