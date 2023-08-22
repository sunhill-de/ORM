<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Storage\Mysql\MysqlAction;
use Sunhill\ORM\Interfaces\HandlesProperties;
use Sunhill\ORM\Storage\Mysql\Utils\PropertyHelpers;
use Sunhill\ORM\Storage\Mysql\Utils\TableManagement;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Storage\Exceptions\IDNotSetException;

class MysqlCollectionUpdate extends MysqlAction implements HandlesProperties
{
    
    use PropertyHelpers, TableManagement;
    
    public function run()
    {
        if (isset($this->additional)) {
            $this->id = $this->additional;
        } else {
            $this->id = $this->collection->getID();
        }
        if (!$this->id) {
            throw new IDNotSetException("Update called but not ID was given.");
        }
        $this->runProperties();
        $this->updateTables();
    }

    protected function updateTables()
    {
        foreach ($this->tables as $table => $fields) {
            $this->updateTable($table, $fields, $this->getIDField($table));
        }
    }
    
    protected function deleteClassTables($list)
    {
        
    }
    
    protected function objectAssigned(int $id)
    {
        
    }
    
    protected function handleArrayOrMap($property)
    {
        $entries = [];
        DB::table($this->getSpecialTableName($property))->where('id',$this->id)->delete();
        foreach ($property->value as $key => $value) {
            switch ($property->element_type) {
                case PropertyObject::class:
                    $entries[] = ['id'=>$this->id,'index'=>$key,'value'=>$value->getID()];
                    $this->objectAssigned($value->getID());
                    break;
                case PropertyCollection::class:
                    $entries[] = ['id'=>$this->id,'index'=>$key,'value'=>$value->getID()];
                    break;
                default:
                    $entries[] = ['id'=>$this->id,'index'=>$key,'value'=>$value];
                    break;
            }
        }
        DB::table($this->getSpecialTableName($property))->insert($entries);
    }
    
    protected function handleLinearField($property, $value)
    {
        if ($property->dirty) {
            $table = $property->owner::getInfo('table');
            $this->addEntry($table, $property->name, $value);
        }
    }
    
    protected function handleSimpleField($property)
    {
        if ($property->dirty) {
            $this->handleLinearField($property, $property->value);
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
        $property->dirty = true;
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