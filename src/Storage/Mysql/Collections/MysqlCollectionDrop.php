<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;
use Sunhill\ORM\Storage\Mysql\Utils\IgnoreSimple;


class MysqlCollectionDrop extends CollectionHandler
{
     
    use IgnoreSimple;
    
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
    
    public function handlePropertyMap(Property $property)
    {
        $this->handlePropertyArray($property);
    }

    public function handlePropertyObject(Property $property)
    {
        
    }    
}