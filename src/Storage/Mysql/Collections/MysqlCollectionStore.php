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

class MysqlCollectionStore extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers;

    protected $tables = [];
    
    protected $id;
    
    public function run()
    {
        $this->runProperties();
        $this->storeMainTable();
        $this->storeOtherTables();
    }

    protected function storeTable($table, $fields)
    {
        DB::table($table)->insert($fields);    
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
            switch ($table) {
                case 'tagobjectassigns':
                    $this->addIDToAllRecords($fields, 'container_id');
                    break;
                case 'attributeobjectassigns':
                    $this->addIDToAllRecords($fields, 'object_id');
                    break;
                default:
                    if (substr($table,0,5) == 'attr_') {
                        $fields['object_id'] = $this->id;
                    } else if (isset($fields[0]) && is_array($fields[0])) {
                        $this->addIDToAllRecords($fields, 'id');
                    } else {
                        $fields['id'] = $this->id;
                    }
                    break;
            }
            $this->storeTable($table, $fields);
        }
    }
    
    protected function addEntry(string $table, string $key, $value)
    {
        if (isset($this->tables[$table])) {
            $this->tables[$table][$key] = $value;
        } else {
            $this->tables[$table] = [$key => $value];
        }
    }
    
    protected function addEntryRecord(string $table, array $data)
    {
        if (isset($this->tables[$table])) {
            $this->tables[$table][] = $data;
        } else {
            $this->tables[$table] = [$data];
        }        
    }
    
    protected function handleLinearField($property, $value)
    {
        $table = $property->owner::getInfo('table');
        $this->addEntry($table, $property->name, $value);        
    }
    
    protected function handleSimpleField($property)
    {
        $this->handleLinearField($property, $property->value);
    }
    
    protected function handleArrayOrMap($property)
    {
        foreach ($property->value as $key => $value) {
            if (($property->element_type == PropertyObject::class) ||
                ($property->element_type == PropertyCollection::class)) {
                    $this->addEntryRecord($this->getSpecialTableName($property), ['index'=>$key,'value'=>$value->getID()]);                    
                } else {
                    $this->addEntryRecord($this->getSpecialTableName($property), ['index'=>$key,'value'=>$value]);                    
                }
        }        
    }
    
    protected function handleInternalReference($property)
    {
        $value = $property->value;
        if (!is_null($value)) {
            $this->handleLinearField($property, $value->getID());
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