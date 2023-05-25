<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Classes;

use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyVarchar;

class MysqlStore
{
    
    use ClassTables;
    
    public function __construct(public $storage) {}

    protected $id = 0;
    
    public function doStore()
    {
        $this->additional_tables = $this->collectAdditionalTables();
        $this->id = $this->storeObject();
        $this->storeTables();
        $this->storeArrays();
        $this->storeTags();
        $this->storeAttributes();
        $this->storeCalculated();
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
    
    protected function storeObject()
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
        return DB::getPdo()->lastInsertId();
    }
    
    private function isSimpleProperty($property)
    {
        return  is_a($property,PropertyDate::class) ||
                is_a($property,PropertyDatetime::class) ||
                is_a($property,PropertyTime::class) ||
                is_a($property,PropertyEnum::class) ||
                is_a($property,PropertyFloat::class) ||
                is_a($property,PropertyInteger::class) ||
                is_a($property,PropertyObject::class) ||
                is_a($property,PropertyText::class) ||
                is_a($property,PropertyVarchar::class);
    }
    
    protected function storeTables()
    {
        $hirarchy = $this->storage->getInheritance();
        array_pop($hirarchy); // remove object

        foreach ($hirarchy as $class) {
            $table = Classes::getTableOfClass($class);
            $namespace = Classes::getNamespaceOfClass($class);
            $values = [
                'id'=>$this->id
            ];
            $properties = $namespace::getPropertyDefinition();
            foreach ($properties as $name => $property) {
                if ($this->isSimpleProperty($property)) {
                    $values[$name] = $this->storage->getEntity($name);
                }
            }
            DB::table($table)->insert($values);
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
    
    protected function storeArrays()
    {
        $array_table = $this->getArrayTables();
        foreach ($array_table as $field => $table) {
            $this->storeArray($field, $table);
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
    
    protected function storeCalculatedField(string $name, string $table)
    {
        DB::table($table)->insert(['id'=>$this->id,'value'=>$this->storage->getEntity($name)]);    
    }
    
    protected function storeCalculated()
    {
        $calc_tables = $this->getCalcTables();
        foreach ($calc_tables as $field => $table) {
            $this->storeCalculatedField($field, $table);
        }
    }
    
}