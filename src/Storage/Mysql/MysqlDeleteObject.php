<?php

namespace Sunhill\ORM\Storage\Mysql;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\ObjectHandler;

/**
 * Helper class to erase an object from the database
 * @author klaus
 *
 */
class MysqlDeleteObject extends ObjectHandler
{
    
    protected $id = 0;
    
    public function doDelete(int $id)
    {
        $this->id = $id;
        $this->run();
    }
    
    public function handleObject()
    {
        DB::table('objects')->where('id',$this->id)->delete();
    }
    
    protected function prepareRun()
    {
        
    }
    
    protected function finishRun()
    {
        $this->deleteAttributes();
        $this->deleteTags();
    }
    
    protected function deleteAttributes()
    {
        $tables = DB::table('attributeobjectassigns')->join('attributes','attributeobjectassigns.attribute_id','=','attributes.id')->where('attributeobjectassigns.object_id',$this->id)->groupBy('attributes.id')->select('attributes.name as name')->get();
        foreach ($tables as $table) {
            DB::table('attr_'.$table->name)->where('object_id',$this->id)->delete(); 
        }
        DB::table('attributeobjectassigns')->where('object_id',$this->id)->delete();
    }
    
    protected function deleteTags()
    {
        DB::table('tagobjectassigns')->where('container_id',$this->id)->delete();
    }
    
    public function handlePropertyText(Property $property)
    {
    }
    
    public function handlePropertyTime(Property $property)
    {
    }
    
    public function handlePropertyBoolean(Property $property)
    {
    }
    
    public function handlePropertyDateTime(Property $property)
    {
    }
    
    public function handlePropertyDate(Property $property)
    {
    }
    
    public function handlePropertyInteger(Property $property)
    {
    }
    
    public function handlePropertyVarchar(Property $property)
    {
    }
    
    public function handlePropertyTimestamp(Property $property)
    {
    }
    
    public function handlePropertyEnum(Property $property)
    {
    }
    
    public function handlePropertyObject(Property $property)
    {
    }
    
    public function handlePropertyFloat(Property $property)
    {
    }
    
    public function handlePropertyArray(Property $property)
    {
        $table = $this->getExtraTableName($property);
        DB::table($table)->where('id',$this->id)->delete();
    }
    
    public function handlePropertyCalculated(Property $property)
    {
    }
    
    public function handlePropertyMap(Property $property)
    {
        $table = $this->getExtraTableName($property);
        DB::table($table)->where('id',$this->id)->delete();
    }
    
    public function handlePropertyTags(Property $property)
    {
    }
    
    public function handlePropertyAttributes(Property $property)
    {
    }
    
    protected function handleClass($class)
    {
        parent::handleClass($class);
        $table = $class::getInfo('table');
        DB::table($table)->where('id',$this->id)->delete();
    }
    
    
}