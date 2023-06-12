<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

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

class MysqlObjectAddColumn extends MysqlObjectMigrateHelper
{
    
    public function __construct(protected $table) {}
    
    protected function getHelpTableName(Property $property)
    {
        return $property->getOwner()::getInfo('table').'_'.$property->getName();
    }
    
    protected function getArrayType(Property $property): string
    {
        switch ($property->getElementType()) {
            case PropertyInteger::class:
                return'integer';
                break;
            case PropertyVarchar::class:
                return'string';
                break;
            case PropertyTime::class:
                return'time';
                break;
            case PropertyText::class:
                return'text';
                break;
            case PropertyObject::class:
                return'integer';
                break;
            case PropertyFloat::class:
                return'float';
                break;
            case PropertyDatetime::class:
                return'datetime';
                break;
            case PropertyDate::class:
                return'date';
                break;
            case PropertyBoolean::class:
                return'bool';
                break;
            case PropertyEnum::class:
                return'string';
                break;
            default:
                throw new PropertyException("It's not possible to build a array of ".$property->getElementType());
        }        
    }
    
    protected function createArrayTable(Property $property, string $type)
    {
        Schema::create($this->getHelpTableName($property), function ($table) use ($type) {
            $table->integer('id');
            $table->$type('value');
            $table->integer('index');
            $table->primary(['id','index']);
        });
    }
    
    protected function handleDefault(Property $property, $field)
    {
        if ($property->getDefaultsNull()) {
            $field->nullable()->default(null);
        } else if (!empty($property->getDefault())) {
            $field->default($property->getDefault());
        }        
        if ($property->getNullable()) {
            $field->nullable();
        }
    }
    
    protected function handleSearchable(Property $property, $field)
    {
        if ($property->getSearchable()) {
            $field = $field->index($this->getHelpTableName($property));
        }        
    }
    
    public function handlePropertyArray(Property $property)
    {
        $this->createArrayTable($property, $this->getArrayType($property));        
    }
    
    public function handlePropertyBoolean(Property $property)
    {
        $field = $this->table->integer($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
        
        return $field;
    }
    
    
    public function handlePropertyCalculated(Property $property)
    {
        $field = $this->table->string($property->getName(),100);
        
        $this->handleSearchable($property, $field);
        $field->nullable(); // otherwise alter column doesn't work
        
        return $field;
    }
    
    
    public function handlePropertyDate(Property $property)
    {
        $field = $this->table->date($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
        
        return $field;
    }
    
    
    public function handlePropertyDateTime(Property $property)
    {
        $field = $this->table->datetime($property->getName());        
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
        
        return $field;
    }
    
    
    public function handlePropertyEnum(Property $property)
    {
        $field = $this->table->string($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
        
        return $field;
    }
    
    
    public function handlePropertyFloat(Property $property)
    {
        $field = $this->table->float($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
        
        return $field;
    }
    
    
    public function handlePropertyInteger(Property $property)
    {
        $field = $this->table->integer($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);
        
        return $field;
    }
    
    
    public function handlePropertyMap(Property $property)
    {
        
    }
    
    
    public function handlePropertyObject(Property $property)
    {
        $field = $this->table->integer($property->getName())->nullable()->default(null);
        
        $this->handleSearchable($property, $field);        
        
        return $field;
    }
    
    
    public function handlePropertyTags(Property $property)
    {
        
    }
    
    
    public function handlePropertyText(Property $property)
    {
        $field = $this->table->text($property->getName());
        
        $this->handleDefault($property, $field);        
        
        return $field;
    }
    
    
    public function handlePropertyTime(Property $property)
    {
        $field = $this->table->time($property->getName());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
        
        return $field;
    }
    
    
    public function handlePropertyTimestamp(Property $property)
    {
        
    }
        
    public function handlePropertyVarchar(Property $property)
    {
        $field = $this->table->string($property->getName(),$property->getMaxLen());
        
        $this->handleDefault($property, $field);
        $this->handleSearchable($property, $field);        
        
        return $field;
    }
    
}