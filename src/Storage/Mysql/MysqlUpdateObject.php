<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Classes;

use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Storage\ObjectHandler;
use Carbon\Carbon;

class MysqlUpdateObject extends ObjectHandler
{
    
    use ClassTables;
    
    protected $id;
      
    protected $values = [];
    
    public function handleObject()
    {
        $data = ['updated_at'=>Carbon::now()->toDateString()];
        if ($this->storage->hasEntity('obj_owner')) {
            $data['obj_owner'] = $this->storage->getEntity('obj_owner');
        }
        if ($this->storage->hasEntity('obj_group')) {
            $data['obj_group'] = $this->storage->getEntity('obj_group');
        }
        if ($this->storage->hasEntity('obj_read')) {
            $data['obj_read'] = $this->storage->getEntity('obj_read');
        }
        if ($this->storage->hasEntity('obj_edit')) {
            $data['obj_edit'] = $this->storage->getEntity('obj_edit');
        }
        if ($this->storage->hasEntity('obj_delete')) {
            $data['obj_delete'] = $this->storage->getEntity('obj_delete');
        }
        DB::table('objects')->where('id',$this->id)->update($data);
    }
    
    protected function prepareRun()
    {
        $this->updateAttributes();
        $this->updateTags();
    }
    
    protected function updateAttributes()
    {
        if (!$this->storage->hasEntity('attributes')) {
            return;
        }
        $values = $this->storage->getEntity('attributes');
        foreach ($values as $value) {
            if ($value->value == null) {
                DB::table('attributevalues')->where(['object_id'=>$this->id,'attribute_id'=>$value->attribute_id])->delete();
            } else if ($value->type == 'text') {
                $insert[] = ['attribute_id'=>$value->attribute_id,'object_id'=>$this->id,'value'=>'','textvalue'=>$value->value];
            } else {
                $insert[] = ['attribute_id'=>$value->attribute_id,'object_id'=>$this->id,'value'=>$value->value,'textvalue'=>''];                    
            }
        }
        if (!empty($insert)) {
            DB::table('attributevalues')->upsert($insert,['attribute_id','object_id'],['value','textvalue']);
        }
    }
    
    protected function updateTags()
    {
        if (!$this->storage->hasEntity('tags')) {
            return;
        }
        DB::table('tagobjectassigns')->where('container_id',$this->id)->delete();
        $values = $this->storage->getEntity('tags')->value;
        $insert = [];
        if (!empty($values)) {
            foreach ($values as $value) {
                $insert[] = ['container_id'=>$this->id,'tag_id'=>$value];
            }
            DB::table('tagobjectassigns')->insert($insert);
        }
    }
    
    /**
     * Add where id = object.id and get the result
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\CollectionHandler::finishRun()
     */
    protected function finishRun()
    {
        foreach ($this->values as $table => $values) {
            DB::table($table)->where('id',$this->id)->update($values);
        }
    }
    
    protected function handleSimpleValue(string $name, string $class)
    {
        if (!$this->storage->hasEntity($name)) {
            return;
        }
        $table = $class::getInfo('table');
        if (isset($this->values[$table])) {
            $this->values[$table][$name] = $this->storage->getEntity($name)->value;
        } else {
            $this->values[$table] = [$name => $this->storage->getEntity($name)->value];            
        }
    }
    
    public function handlePropertyText(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyTime(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyBoolean(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyDateTime(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyDate(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyInteger(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyVarchar(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyTimestamp(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyEnum(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyObject(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyFloat(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyArray(Property $property)
    {
        if (!$this->storage->hasEntity($property->getName())) {
            return;
        }
        $target_table = $this->getExtraTableName($property); 
        DB::table($target_table)->where('id',$this->id)->delete();
        $values = $this->storage->getEntity($property->getName());
        $insert = [];
        $index = 0;
        foreach ($values->value as $value) {
            $insert[] = ['id'=>$this->id,'value'=>$value,'index'=>$index++];
        }
        DB::table($target_table)->insert($insert);
    }
    
    public function handlePropertyCalculated(Property $property)
    {
        $this->handleSimpleValue($property->getName(),$property->getOwner());
    }
    
    public function handlePropertyMap(Property $property)
    {
        if (!$this->storage->hasEntity($property->getName())) {
            return;
        }
    }
    
    public function handlePropertyTags(Property $property)
    {
    }
    
    public function handlePropertyAttributes(Property $property)
    {
    }
    
    public function doUpdate(int $id)
    {
        $this->id = $id;
        $this->additional_tables = $this->collectAdditionalTables();
        $this->run();
    }
        
}