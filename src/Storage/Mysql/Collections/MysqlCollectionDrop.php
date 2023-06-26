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
        Schema::drop($class);
    }
    
    protected function prepareRun()
    {
        
    }
    
    protected function finishRun()
    {
        
    }
    
    public function handlePropertyArray($property)
    {
        Schema::drop($this->getExtraTableName($property));
    }
    
    public function handlePropertyMap($property)
    {
        $this->handlePropertyArray($property);
    }

    public function handlePropertyObject($property)
    {
        
    }   
    
    public function handlePropertyInformation($property)
    {
        
    }
    
    public function handlePropertyExternalReference($property)
    {
        
    }
    
    public function handlePropertyCollection($property)
    {
        
    }
    
    public function handlePropertyKeyfield($property)
    {
        
    }
    
}