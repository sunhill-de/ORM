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

class MysqlCollectionMigrateFresh extends MysqlCollectionMigrateBase
{
    
    use PropertyHelpers;
    
    protected $table;
    
    protected $main_table_name;
    
    public function run()
    {
        if (($this->main_table_name = ($this->collection)::getInfo('table')) == 'objects') {
            return;
        }
        Schema::create($this->main_table_name, function($table) {
            $table->integer('id')->primary();
            $this->table = $table;
            $this->runProperties(true);
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