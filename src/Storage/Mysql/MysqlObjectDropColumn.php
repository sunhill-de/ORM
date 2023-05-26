<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyException;

class MysqlObjectDropColumn extends MysqlObjectMigrateHelper
{
    
    protected function getHelpTableName(Property $property)
    {
        return $property->getOwner()::getInfo('table').'_'.$property->getName();
    }

    protected function isColumnDropped(Property $property)
    {
        
    }

    protected function dropColumnInDatabase(Property $property)
    {
        Schema::dropColumns($property->getOwner()::getInfo('table'),[$property->getName()]);        
    }
    
    public function handlePropertyArray(Property $property)
    {
        if ($this->isColumnDropped($property)) {
            
        }
    }
    
    public function handlePropertyBoolean(Property $property)
    {
        $field = $this->table->integer($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
    }
    
    
    public function handlePropertyCalculated(Property $property)
    {
        $field = $this->table->string($property->getName(),100);
        
        $this->handleSearchable($property, $field);
    }
    
    
    public function handlePropertyDate(Property $property)
    {
        $field = $this->table->date($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
    }
    
    
    public function handlePropertyDateTime(Property $property)
    {
        $field = $this->table->datetime($property->getName());        
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
    }
    
    
    public function handlePropertyEnum(Property $property)
    {
        $field = $this->table->string($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
    }
    
    
    public function handlePropertyFloat(Property $property)
    {
        $field = $this->table->date($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
    }
    
    
    public function handlePropertyInteger(Property $property)
    {
        $field = $this->table->integer($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
    }
    
    
    public function handlePropertyMap(Property $property)
    {
        
    }
    
    
    public function handlePropertyObject(Property $property)
    {
        $field = $this->table->integer($property->getName())->nullable()->default(null);
        
        $this->handleSearchable($property, $field);        
    }
    
    
    public function handlePropertyTags(Property $property)
    {
        
    }
    
    
    public function handlePropertyText(Property $property)
    {
        $field = $this->table->text($property->getName());
        
        $this->handleDefault($property, $field);        
    }
    
    
    public function handlePropertyTime(Property $property)
    {
        $field = $this->table->time($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
    }
    
    
    public function handlePropertyTimestamp(Property $property)
    {
        
    }
        
    public function handlePropertyVarchar(Property $property)
    {
        $field = $this->table->string($property->getName(),$property->getMaxLen());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
    }
    
}