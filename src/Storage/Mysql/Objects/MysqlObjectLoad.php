<?php

namespace Sunhill\ORM\Storage\Mysql\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Storage\ObjectHandler;
use Sunhill\ORM\Properties\Property;

/**
 * Helper class to load an object out of the database
 * @author klaus
 *
 */
class MysqlObjectLoad extends ObjectHandler
{
    
    protected $id;
    
    protected $group_query;
    
    public function handleObject()
    {
        $this->group_query = DB::table('objects');
    }
    
    protected function prepareRun()
    {
        $this->loadAttributes();
        $this->loadTags();
    }
    
    protected function loadAttributes()
    {
        $result = [];
        $query = DB::table('attributeobjectassigns')->join('attributes','attributes.id','=','attributeobjectassigns.attribute_id')->where('object_id',$this->id)->get();
        foreach ($query as $attribute) {
            $val_query = DB::table('attr_'.$attribute->name)->where('object_id',$this->id)->first();
            $entry = new \StdClass();
            $entry->allowed_classes = $attribute->allowed_classes;
            $entry->name = $attribute->name;
            $entry->attribute_id = $attribute->attribute_id;
            $entry->type = $attribute->type;
            $entry->value = $val_query->value;
            $result[$attribute->name] = $entry;
        }        
        $this->storage->setEntity('attributes',$result);        
    }
    
    protected function loadTags()
    {
        $this->storage->setEntity('tags',array_column(DB::table('tagobjectassigns')->where('container_id',$this->id)->get()->toArray(),'tag_id'));        
    }
    
    /**
     * Add where id = object.id and get the result
     * {@inheritDoc}
     * @see \Sunhill\ORM\Storage\CollectionHandler::finishRun()
     */
    protected function finishRun()
    {
        if (!($result = $this->group_query->where('objects.id',$this->id)->first())) {
            throw \Exception("object with id ".$this->id." not loadable.");
        }
        foreach ($result as $key => $value) {
            $this->storage->setEntity($key, $value);            
        }
    }
    
    public function handlePropertyText(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyTime(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyBoolean(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyDateTime(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyDate(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyInteger(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyVarchar(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyTimestamp(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyEnum(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyObject(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyFloat(Property $property)
    {
        // Do nothing
    }
    
    public function handlePropertyArray(Property $property)
    {
        $table = $this->getExtraTableName($property);
        $result = array_column(DB::table($table)->where('id',$this->id)->get()->toArray(),'value');
        $this->storage->setEntity($property->getName(), $result);
    }
    
    public function handlePropertyCalculated(Property $property)
    {
        // Do nothing
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
    
    protected function handleClass($class)
    {
        parent::handleClass($class);
        $table = $class::getInfo('table');
        $this->group_query = $this->group_query->join($table,'objects.id','=',$table.'.id');
    }
    
    public function doLoad(int $id)
    {
        $this->id = $id;
        $this->run();        
    }
            
}