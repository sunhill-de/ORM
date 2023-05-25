<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Storage\ObjectHandler;
use Sunhill\ORM\Properties\Property;

class MysqlStoreObject extends ObjectHandler
{
        
    protected $id = 0;
    
    protected $values = [];
    
    public function doStore()
    {
        $this->run();
        return $this->id;
    }
    
    /**
     * Method taken from https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     * Generates a v4 uuid string and returns it
     */
    protected function generateUUID()
    {
        $data = random_bytes(16);
        
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        
    }
    
    public function handleObject()
    {
        DB::table('objects')->insert([
            'classname'=>$this->storage->getCaller()::getInfo('name'),
            'uuid'=>$this->generateUUID(),
            'obj_owner'=>!empty($this->storage->getEntity('obj_owner'))?$this->storage->getEntity('obj_owner'):ORMObject::DEFAULT_OWNER,
            'obj_group'=>!empty($this->storage->getEntity('obj_group'))?$this->storage->getEntity('obj_group'):ORMObject::DEFAULT_GROUP,
            'obj_read'=>!empty($this->storage->getEntity('obj_read'))?$this->storage->getEntity('obj_read'):ORMObject::DEFAULT_READ,
            'obj_edit'=>!empty($this->storage->getEntity('obj_edit'))?$this->storage->getEntity('obj_edit'):ORMObject::DEFAULT_EDIT,
            'obj_delete'=>!empty($this->storage->getEntity('obj_delete'))?$this->storage->getEntity('obj_delete'):ORMObject::DEFAULT_DELETE,
        ]);
        $this->id = DB::getPdo()->lastInsertId();        
    }
    
    protected function prepareRun()
    {
        
    }
    
    protected function finishRun()
    {
        foreach ($this->values as $table => $values) {
            DB::table($table)->insert($values);
        }
        $this->storeAttributes();
        $this->storeTags();
    }
    
    protected function storeAttributes()
    {
        $data = [];
        if (empty($entities = $this->storage->getEntity('attributes'))) {
            return;
        }
        foreach ($entities as $entity) {
            if ($entity->type == 'text') {
                $data[] = [
                    'attribute_id'=>$entity->attribute_id,
                    'object_id'=>$this->id,
                    'value'=>'',
                    'textvalue'=>$entity->value
                ];
            } else {
                $data[] = [
                    'attribute_id'=>$entity->attribute_id,
                    'object_id'=>$this->id,
                    'value'=>$entity->value,
                    'textvalue'=>''
                ];
            }
        }
        if (!empty($data)) {
            DB::table('attributevalues')->insert($data);
        }
    }
    
    protected function storeTags()
    {
        $data = [];
        if (empty($tags = $this->storage->getEntity('tags'))) {
            return;
        }
        foreach ($tags as $tag) {
            $data[] = ['container_id'=>$this->id,'tag_id'=>$tag];
        }
        if (!empty($data)) {
            DB::table('tagobjectassigns')->insert($data);
        }
    }
    
    protected function storeArray(string $field, string $table)
    {
        $data = [];
        $index = 0;
        foreach ($this->storage->getEntity($field) as $value) {
            $data[] = ['id'=>$this->id,'value'=>$value,'index'=>$index++];
        }
        if (!empty($data)) {
            DB::table($table)->insert($data);
        }
    }
    
    protected function handleSimpleField(Property $property)
    {
        $table = $property->getOwner()::getInfo('table');
        $name = $property->getName();
        $value = $this->storage->getEntity($name); 
        
        if (isset($this->values[$table])) {
            $this->values[$table][$name] = $value;
        } else {
            $this->values[$table] = ['id'=>$this->id, $name => $value];
        }
    }
    
    public function handlePropertyText(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyTime(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyBoolean(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyDateTime(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyDate(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyInteger(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyVarchar(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyTimestamp(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyEnum(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyObject(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyFloat(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyArray(Property $property)
    {
        $table = $this->getExtraTableName($property);
        $values = [];
        $index = 0;
        foreach ($this->storage->getEntity($property->getName()) as $value) {
            $values[] = ['id'=>$this->id,'value'=>$value,'index'=>$index++];
        }
        DB::table($table)->insert($values);
    }
    
    public function handlePropertyCalculated(Property $property)
    {
        $this->handleSimpleField($property);
    }
    
    public function handlePropertyMap(Property $property)
    {
        $table = $this->getExtraTableName($property);
        $result = array_column(DB::table($table)->where('id',$this->id)->get()->toArray(),'value');
        $this->storage->setEntity($property->getName(), $result);
    }
    
    public function handlePropertyTags(Property $property)
    {
    }
    
    public function handlePropertyAttributes(Property $property)
    {
    }
    
}