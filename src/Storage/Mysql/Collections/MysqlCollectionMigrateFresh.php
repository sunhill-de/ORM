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

class MysqlCollectionMigrateFresh extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers;
    
    protected $table;
    
    protected $main_table_name;
    
    public function run()
    {
        $this->main_table_name = ($this->collection)::getInfo('table');
        Schema::create($this->main_table_name, function($table) {
            $this->table = $table;
            $this->runProperties();
        });
    }
    
    protected function handleCommonProperties($field, $property)
    {
        if ($property->searchable) {
            $field->index();
        }
        if (!is_null($property->default)) {
            if (is_a($property->default, DefaultNull::class)) {
                $field->nullable()->default(null);
            } else {
                $field->default($property->default);
            }
        }
        if ($property->nullable) {
            $field->nullable();
        }
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
    
    public function handlePropertyArray($property)
    {
        $this->createArrayOrMap($property, false);
    }
    
    public function handlePropertyBoolean($property)
    {
        $this->handleCommonProperties($this->table->integer($property->name), $property);
    }
    
    public function handlePropertyCalculated($property)
    {
        $this->handleCommonProperties($this->table->string($property->name,200), $property);
    }
    
    public function handlePropertyCollection($property)
    {
        $this->handleCommonProperties($this->table->integer($property->name), $property);
    }
    
    public function handlePropertyDate($property)
    {
        $this->handleCommonProperties($this->table->date($property->name), $property);
    }
    
    public function handlePropertyDateTime($property)
    {
        $this->handleCommonProperties($this->table->datetime($property->name), $property);
    }
    
    public function handlePropertyEnum($property)
    {
        $this->handleCommonProperties($this->table->string($property->name,100), $property);
    }
    
    public function handlePropertyExternalReference($property)
    {
        $this->handleCommonProperties($this->table->integer($property->name), $property);        
    }
    
    public function handlePropertyFloat($property)
    {
        $this->handleCommonProperties($this->table->float($property->name), $property);
    }
    
    public function handlePropertyInformation($property)
    {
        $this->handleCommonProperties($this->table->string($property->name,100), $property);        
    }
    
    public function handlePropertyInteger($property)
    {
        $this->handleCommonProperties($this->table->integer($property->name), $property);
    }
    
    public function handlePropertyKeyfield($property)
    {
        
    }
    
    public function handlePropertyMap($property)
    {
        $this->createArrayOrMap($property, true);
    }
    
    public function handlePropertyObject($property)
    {
        $this->handleCommonProperties($this->table->integer($property->name), $property);
    }
    
    public function handlePropertyTags($property)
    {
    }
    
    public function handlePropertyText($property)
    {
        $this->handleCommonProperties($this->table->text($property->name), $property);
    }
    
    public function handlePropertyTime($property)
    {
        $this->handleCommonProperties($this->table->time($property->name), $property);
    }
    
    public function handlePropertyVarchar($property)
    {
        $this->handleCommonProperties(
            $this->table->string($property->name, $property->max_len), 
            $property
        );
    }
}