<?php

namespace Sunhill\ORM\Storage\Mysql;

use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;

abstract class MysqlObjectMigrateHelper implements HandlesProperties
{
    
    public function handleProperty(Property $property)
    {
        switch ($property::class) {
            case PropertyArray::class:
                return $this->handlePropertyArray($property);
            case PropertyBoolean::class:
                return $this->handlePropertyBoolean($property);
            case PropertyCalculated::class:
                return $this->handlePropertyCalculated($property);
            case PropertyDate::class:
                return $this->handlePropertyDate($property);
            case PropertyDatetime::class:
                return $this->handlePropertyDateTime($property);
            case PropertyEnum::class:
                return $this->handlePropertyEnum($property);
            case PropertyFloat::class:
                return $this->handlePropertyFloat($property);
            case PropertyInteger::class:
                return $this->handlePropertyInteger($property);
            case PropertyMap::class:
                return $this->handlePropertyMap($property);
            case PropertyObject::class:
                return $this->handlePropertyObject($property);
            case PropertyText::class:
                return $this->handlePropertyText($property);
            case PropertyTime::class:
                return $this->handlePropertyTime($property);
            case PropertyVarchar::class:
                return $this->handlePropertyVarchar($property);
            default:
                throw PropertyException("Can't handle a column of type ".$property::class);
        }
    }
    
}