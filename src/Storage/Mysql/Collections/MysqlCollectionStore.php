<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;
use Sunhill\ORM\Storage\Mysql\Utils\IgnoreSimple;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyCalculated;

use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Storage\Mysql\Utils\TableManagement;
use Sunhill\ORM\Properties\Utils\DefaultNull;

class MysqlCollectionStore extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers, TableManagement;
    
    public function run()
    {
        $this->runProperties();
        $this->storeMainTable();
        $this->storeOtherTables();
    }

    protected function storeMainTable()
    {
        $this->storeTable($this->collection::getInfo('table'), $this->tables[$this->collection::getInfo('table')]);        
        $this->id = DB::getPdo()->lastInsertId();
        $this->collection->setID($this->id);
        unset($this->tables[$this->collection::getInfo('table')]);
    }
    
    protected function addIDToAllRecords(&$fields, string $id_field_name)
    {
        for ($i=0;$i<count($fields);$i++) {
            $fields[$i][$id_field_name] = $this->id;
        }
    }
    
    protected function storeOtherTables()
    {
        foreach ($this->tables as $table => $fields) {
            $this->appendID($table, $fields);
            $this->storeTable($table, $fields);
        }
    }
    
    protected function handleLinearField($property, $value)
    {
        $table = $property->owner::getInfo('table');
        $this->addEntry($table, $property->name, $value);        
    }
    
    protected function handleSimpleField($property)
    {
        if (!isset($property->value)) {
            if (!$property->initialized && is_null($property->default)) {
                throw new \Exception("The value for '".$property->name."' is not set.");
            }
        }
        $this->handleLinearField($property, $property->value);
    }
    
    protected function objectAssigned(int $object_id)
    {
        
    }
    
    protected function handleArrayOrMap($property)
    {
        foreach ($property->value as $key => $value) {
            switch ($property->element_type) {
                case PropertyObject::class:
                    $this->addEntryRecord($this->getSpecialTableName($property), ['index'=>$key,'value'=>$value->getID()]);
                    $this->objectAssigned($value->getID());
                    break;
                case PropertyCollection::class:
                    $this->addEntryRecord($this->getSpecialTableName($property), ['index'=>$key,'value'=>$value->getID()]);
                    break;
                default:
                    $this->addEntryRecord($this->getSpecialTableName($property), ['index'=>$key,'value'=>$value]);
                    break;
            }
        }        
    }
    
    protected function handleInternalReference($property)
    {
        $value = $property->value;
        if (!is_null($value)) {
            $this->handleLinearField($property, $value->getID());
            $this->objectAssigned($value->getID());
        } else {
            $this->handleLinearField($property, null);
        }        
    }
    
    public function handlePropertyArray($property)
    {
        $this->handleArrayOrMap($property);
    }
    
    public function handlePropertyBoolean($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyCalculated($property)
    {
        $property_obj = $this->collection->getProperty($property->name);
        $property_obj->recalculate();
        $this->handleLinearField($property, $property_obj->getValue());
    }
    
    public function handlePropertyCollection($property)
    {
        $this->handleInternalReference($property);
    }
    
    public function handlePropertyDate($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyDateTime($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyEnum($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyExternalReference($property)
    {
        
    }
    
    public function handlePropertyFloat($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyInformation($property)
    {
        
    }
    
    public function handlePropertyInteger($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyKeyfield($property)
    {
        
    }
    
    public function handlePropertyMap($property)
    {
        $this->handleArrayOrMap($property);
    }
    
    public function handlePropertyObject($property)
    {
        $this->handleInternalReference($property);
    }
    
    public function handlePropertyTags($property)
    {
    }
    
    public function handlePropertyText($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyTime($property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyVarchar($property)
    {
        $this->handleSimpleField($property);
    }
}