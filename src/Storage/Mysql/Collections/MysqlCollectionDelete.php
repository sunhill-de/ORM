<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;


class MysqlCollectionDelete extends CollectionHandler
{
        
    protected $id = 0;
    
    public function doDelete(int $id)
    {
        $this->id = $id;
        $this->run();
    }
    
    protected function handleClass(string $class)
    {
        parent::handleClass($class);
        DB::table($class::getInfo('table'))->where('id',$this->id)->delete();
    }
    
    protected function prepareRun()
    {
        
    }
    
    protected function finishRun()
    {
        
    }
    
    public function handlePropertyArray(Property $property)
    {
        $table = $this->getExtraTableName($property);
        DB::table($table)->where('id',$this->id)->delete();
    }
    
    public function handlePropertyBoolean(Property $property)
    {
        
    }
    
    public function handlePropertyCalculated(Property $property)
    {
        
    }
    
    public function handlePropertyDate(Property $property)
    {
        
    }
    
    public function handlePropertyDateTime(Property $property)
    {
        
    }
    
    public function handlePropertyEnum(Property $property)
    {
        
    }
    
    public function handlePropertyFloat(Property $property)
    {
        
    }
    
    public function handlePropertyInteger(Property $property)
    {
        
    }
    
    public function handlePropertyMap(Property $property)
    {
        $table = $this->getExtraTableName($property);
        DB::table($table)->where('id',$this->id)->delete();
    }
    
    public function handlePropertyObject(Property $property)
    {
        
    }
    
    public function handlePropertyTags(Property $property)
    {
        
    }
    
    public function handlePropertyText(Property $property)
    {
        
    }
    
    public function handlePropertyTime(Property $property)
    {
        
    }
    
    public function handlePropertyTimestamp(Property $property)
    {
        
    }
    
    public function handlePropertyVarchar(Property $property)
    {
        
    }
    
}