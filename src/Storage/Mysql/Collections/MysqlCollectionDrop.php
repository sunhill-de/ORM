<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;


class MysqlCollectionDrop extends CollectionHandler
{
        
    protected $id = 0;
    
    public function doDrop()
    {
        $this->run();
    }
    
    protected function handleClass(string $class)
    {
        parent::handleClass($class);
        Schema::drop($class::getInfo('table'));
    }
    
    protected function prepareRun()
    {
        
    }
    
    protected function finishRun()
    {
        
    }
    
    public function handlePropertyArray(Property $property)
    {
        Schema::drop($this->getExtraTableName($property));
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
        Schema::drop($this->getExtraTableName($property));
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