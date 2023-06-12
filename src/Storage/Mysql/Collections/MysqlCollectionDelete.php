<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;
use Sunhill\ORM\Storage\Mysql\Utils\IgnoreSimple;


class MysqlCollectionDelete extends CollectionHandler
{
    
    use IgnoreSimple;
    
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
    
    public function handlePropertyMap(Property $property)
    {
        $this->handlePropertyArray($property);
    }
    
    public function handlePropertyObject(Property $property)
    {
        
    }
    
}