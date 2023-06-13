<?php

namespace Sunhill\ORM\Storage\Mysql\Collections;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Storage\CollectionHandler;
use Sunhill\ORM\Storage\Mysql\Utils\IgnoreSimple;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyCalculated;


class MysqlCollectionStore extends CollectionHandler
{
    
    use IgnoreSimple;
    
    protected $id = 0;
    
    protected $storage_id;
    
    protected function prepareRun()
    {
        
    }
    
    protected function finishRun()
    {
    }
    
    public function doStore(): int
    {
        $this->run();
        return $this->id;
    }
    
    protected function handleClass(string $storage_id)
    {
        $this->storage_id = $storage_id;
        
        $data = [];
        foreach ($this->storage->getEntitiesOfStorageID($storage_id) as $key => $value) {
            switch ($value->type) {
                case PropertyVarchar::class:
                case PropertyInteger::class:
                case PropertyBoolean::class:
                case PropertyObject::class:
                case PropertyDate::class:
                case PropertyTime::class:
                case PropertyDatetime::class:
                case PropertyFloat::class:
                case PropertyEnum::class:
                case PropertyText::class:
                case PropertyCalculated::class:                    
                    $data[$key] = $value->value;
                    break;
            }
        }
        $this->id = DB::table($storage_id)->insertGetId($data);
    }
        
    public function handlePropertyArray($property)
    {
        $i=0;
        $data = [];
        foreach ($property->value as $key => $value) {
            $entry = ['id'=>$this->id,'index'=>$key,'value'=>$value];
            $data[] = $entry;
        }
        DB::table($this->getExtraTableName($property))->insert($data);
    }
    
    public function handlePropertyMap($property)
    {
        $this->handlePropertyArray($property);
    }
    
    public function handlePropertyObject($property)
    {
        
    }
        
}