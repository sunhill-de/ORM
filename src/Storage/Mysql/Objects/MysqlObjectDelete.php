<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\ObjectHandler;
use Sunhill\ORM\Storage\Mysql\Collections\MysqlCollectionDelete;

/**
 * Helper class to erase an object from the database
 * @author klaus
 *
 */
class MysqlObjectDelete extends MysqlCollectionDelete
{
   
    protected function deleteAttributes()
    {
        $query = DB::table('attributeobjectassigns')->join('attributes','id','=','attribute_id')->where('object_id',$this->additional)->get();
        foreach ($query as $attribute) {
            DB::table('attr_'.$attribute->name)->where('object_id',$this->additional)->delete();            
        }
        DB::table('attributeobjectassigns')->where('object_id',$this->additional)->delete();
    }
    
    public function run()
    {
        parent::run();
        $this->deleteAttributes();
    }
    
    public function handlePropertyInformation($property)
    {
    }
    
    public function handlePropertyTags($property)
    {
        DB::table('tagobjectassigns')->where('container_id',$this->additional)->delete();
    }
    
}