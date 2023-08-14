<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

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
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionUpdate;

class MysqlObjectUpdate extends MysqlCollectionUpdate
{
    
    protected $target_objects = [];
    
    protected function objectAssigned(int $id)
    {
        if (!in_array($id,$this->target_objects)) {
            $this->target_objects[] = $id;
        }
    }
    
    public function handleAttribute($property)
    {
        if (is_null($property->value)) {
            DB::table('attributeobjectassigns')->where('object_id',$this->id)->where('attribute_id',$property->attribute_id)->delete();
            DB::table('attr_'.$property->name)->where('object_id', $this->id)->delete();
        } else  {
            DB::table('attributeobjectassigns')->insertOrIgnore(['object_id'=>$this->id,'attribute_id'=>$property->attribute_id]);   
            DB::table('attr_'.$property->name)->upsert(['object_id'=>$this->id,'value'=>$property->value],['object_id'],['value']);
        }
    }
    
    public function handlePropertyInformation($property)
    {
    }
    
    public function handlePropertyTags($property)
    {
        DB::table('tagobjectassigns')->where('container_id', $this->id)->delete();
        $data = [];
        foreach ($property->value as $tag) {
            $data[] = ['container_id'=>$this->id, 'tag_id'=>$tag->getID()];
        }
        DB::table('tagobjectassigns')->insert($data);
    }
    
    protected function storeObjectAssigns()
    {
        $data = [];
        DB::table('objectobjectassigns')->where('container_id',$this->id)->delete();
        foreach ($this->target_objects as $object) {
            $data[] = ['container_id'=>$this->id,'target_id'=>$object];
        }
        DB::table('objectobjectassigns')->insert($data);
    }
    
    protected function updateTables()
    {
        parent::updateTables();
        $this->storeObjectAssigns();
    }
}