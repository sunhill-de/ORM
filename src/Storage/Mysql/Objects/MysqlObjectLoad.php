<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Storage\ObjectHandler;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionLoad;
use Sunhill\ORM\Objects\Tag;

/**
 * Helper class to load an object out of the database
 * @author klaus
 *
 */
class MysqlObjectLoad extends MysqlCollectionLoad
{
    
    protected function loadAttributes()
    {
        $query = DB::table('attributeobjectassigns')->join('attributes','attribute_id','=','id')->where('object_id',$this->collection->getID())->get();
        foreach ($query as $attribute) {
            $data = DB::table('attr_'.$attribute->name)->where('object_id',$this->collection->getID())->first();
            $property = $this->collection->dynamicAddProperty($attribute->name, $attribute->type);
            $property->loadValue($data->value);
        }
    }
    
    public function run()
    {
        parent::run();
        $this->loadAttributes();
    }
    
    public function handlePropertyInformation($property)
    {
        $name = $property->name;
        $path = $this->data->$name;
        $this->collection->getProperty($name)->setPath($path);
    }
    
    public function handlePropertyTags($property)
    {
         $query = DB::table('tagobjectassigns')->where('container_id',$this->collection->getID())->get();
         foreach ($query as $tag) {
             $tag_obj = new Tag();
             $tag_obj->load($tag->tag_id);
             $this->collection->tags[] = $tag_obj;
         }
    }
        
}