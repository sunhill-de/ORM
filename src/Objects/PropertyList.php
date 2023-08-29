<?php
/**
 * @file PropertyList.php
 * Defines a helping class for the definition of property collections 
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2023-03-21
 * Localization: none
 * Documentation: in progress
 * Tests: none
 * Coverage: 90% (2023-08-26)
 * PSR-State: some type hints missing
 * Tests: PropertyCollection_infoTest
 */
namespace Sunhill\ORM\Objects;

use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyTimestamp;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Properties\PropertyCalculated;
use Sunhill\ORM\Properties\PropertyTags;
use Sunhill\ORM\Properties\PropertyKeyfield;
use Sunhill\ORM\Properties\PropertyExternalReference;
use Sunhill\ORM\Properties\PropertyInformation;

/**
 * Basic class for all classes that have properties.
 *  
 * @author lokal
 */
class PropertyList  
{
    protected $list = [];
    
    public function __construct(protected $owner) {}
    
    protected function checkForDuplicate(string $name)
    {
        if (isset($this->list[$name])) {
            throw new PropertyException("Duplicate property name '$name'");
        }
    }

    public function addProperty(string $class, string $name)
    {
        $this->checkForDuplicate($name);
        $property = new $class();
        $property->setName($name);
        if (!is_null($this->owner)) {
            $property->setOwner($this->owner);        
        }
        
        $this->list[$name] = $property;
        
        return $property;
    }
    
    public function forceAddProperty(string $class, string $name)
    {
        $property = new $class();
        $property->forceName($name);
        if (!is_null($this->owner)) {
            $property->setOwner($this->owner);
        }
        
        $this->list[$name] = $property;
        
        return $property;        
    }
    
    /**
     * Adds an integer field to the property list
     * 
     * @param $name string The name of the Property
     */
    public function integer(string $name): PropertyInteger
    {
        return $this->addProperty(PropertyInteger::class, $name);
    }

    /**
     * Adds an character field to the property list
     * 
     * @param $name string The name of the Property
     * @param $maxlen int The maximum length of the strings
     */
    public function varchar(string $name, int $maxlen = 255): PropertyVarchar
    {
        return $this->addProperty(PropertyVarchar::class, $name)->setMaxLen($maxlen);
    }
    
    /**
     * Alias for varchar()
     * 
     * @param $name string The name of the Property
     * @param $maxlen int The maximum length of the strings
     */
    public function string(string $name, int $maxlen = 255): PropertyVarchar
    {
        return $this->varchar($name, $maxlen);
    }
    
    public function float(string $name): PropertyFloat
    {
        return $this->addProperty(PropertyFloat::class, $name);
    }
    
    public function boolean(string $name): PropertyBoolean
    {
        return $this->addProperty(PropertyBoolean::class, $name);
    }
    
    public function date(string $name): PropertyDate
    {
        return $this->addProperty(PropertyDate::class, $name);
    }
    
    public function datetime(string $name): PropertyDatetime
    {
        return $this->addProperty(PropertyDatetime::class, $name);        
    }
    
    public function time(string $name): PropertyTime
    {
        return $this->addProperty(PropertyTime::class, $name);        
    }
    
    public function text(string $name): PropertyText
    {
        return $this->addProperty(PropertyText::class, $name);        
    }
    
    public function array(string $name): PropertyArray
    {
        return $this->addProperty(PropertyArray::class, $name);        
    }
    
    /**
     * @deprecated use array()
     * @param string $name
     * @return PropertyArray
     */
    public function arrayOfStrings(string $name): PropertyArray
    {
        return $this->array($name, PropertyVarchar::class);    
    }
        
    /**
     * @deprecated use array()
     * @param string $name
     * @return PropertyArray
     */
    public function arrayOfObjects(string $name): PropertyArray
    {
        return $this->array($name, PropertyObject::class);
    }
    
    public function object(string $name): PropertyObject
    {
        return $this->addProperty(PropertyObject::class, $name);    
    }
    
    public function map(string $name): PropertyMap
    {
        return $this->addProperty(PropertyMap::class, $name);
    }
    
    public function keyfield(string $name, string $build_rule)
    {
        return $this->addProperty(PropertyKeyfield::class, $name)->setBuildRule($build_rule);
    }
    
    public function collection(string $name): PropertyCollection
    {
        return $this->addProperty(PropertyCollection::class, $name);
    }
    
    public function externalReference(string $name, string $table_name, string $key_field = 'id'): PropertyExternalReference
    {
        return $this->addProperty(PropertyExternalReference::class, $name)->setExternalTable($table_name)->setExternalKey($key_field);
    }
    
    public function enum(string $name, array $allowed_keys = []): PropertyEnum
    {
        return $this->addProperty(PropertyEnum::class, $name)->setEnumValues($allowed_keys);
    }
    
    public function calculated(string $name, $callback = null): PropertyCalculated
    {
        $property = $this->addProperty(PropertyCalculated::class, $name);
        if (!is_null($callback)) {
            $property->setCallback($callback);
        }
        return $property;
    }
    
    public function tags(): PropertyTags
    {
        return $this->addProperty(PropertyTags::class, 'tags');        
    }
    
    public function information(string $name)
    {
        return $this->addProperty(PropertyInformation::class, $name);    
    }
    
    public function toArray(): array
    {
        return $this->list;
    }
}
