<?php

namespace Sunhill\ORM\Tests\Feature\Objects;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\PropertyList;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Utils\ObjectList;

class FakeStorage extends StorageBase
{
    
    protected function doLoad(int $id)
    {
        $this->entities = $this->caller->storage_values;
    }
    
    protected function doStore(): int
    {
        if (isset($this->entities['attributes'])) {
            foreach ($this->entities['attributes'] as $attribute) {
                $this->entities['attributes'][$attribute['name']]['value_id'] = 9;
            }
        }
        
    }
    
    protected function doUpdate(int $id)
    {
        $this->caller->storage_values = $this->entities;
    }
    
    protected function doDelete(int $id)
    {
        
    }
    
    protected function doMigrate()
    {
        
    }
    
    protected function doPromote()
    {
        
    }
    
    protected function doDegrade()
    {
        
    }
    
    protected function doSearch()
    {
        
    }
    
    protected function doDrop()
    {
        
    }
        
}

class ObjectUnit extends ORMObject
{
    
    public $storage_values;
    
    protected function createStorage(): StorageBase
    {
        return new FakeStorage($this);
    }
    
    protected static function setupProperties(PropertyList $list)
    {
        $list->integer('intvalue');
        $list->object('objectvalue')->setAllowedClasses(['dummy'])->setDefault(null);
        $list->arrayofstrings('sarray');
        $list->arrayOfObjects('oarray')->setAllowedClasses(['dummy']);
        $list->calculated('calcvalue');
    }
    
    public function calculate_calcvalue()
    {
        return $this->intvalue."A";
    }
    
    public function publicLoad($id)
    {
        $this->load($id);
    }
}

