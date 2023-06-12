<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;
use Sunhill\ORM\Storage\Mysql\Utils\IgnoreSimple;

/**
 * Helper class to load an object out of the database
 * @author klaus
 *
 */
class MysqlCollectionLoad extends CollectionHandler
{
    
    use IgnoreSimple;
    
    protected $id = 0;
    
    protected function prepareRun()
    {
        
    }
    
    protected function finishRun()
    {
        
    }
    
    public function doLoad(int $id)
    {
        $this->id = $id;
        return $this->run();
    }
    
    protected function handleClass(string $storage_id)
    {        
        $query = DB::table($storage_id)->where('id',$this->id)->first();
        foreach ($query as $key => $value) {
            $this->storage->setEntity($key, $value);
        }
    }
    
    public function handlePropertyArray($property)
    {
        $query = DB::table($this->getExtraTableName($property))->where('id',$this->id)->get();
        $result = [];
        foreach ($query as $entry) {
            $result[$entry->index] = $entry->value;
        }
        $this->storage->setEntity($property->name, $result);
    }
    
    public function handlePropertyMap($property)
    {
        $this->handlePropertyArray($property);        
    }
    
    public function handlePropertyObject($property)
    {
        
    }
    
        
}