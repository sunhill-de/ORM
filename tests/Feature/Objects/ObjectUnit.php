<?php

namespace Sunhill\ORM\Tests\Feature\Objects;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Storage\StorageBase;

class FakeStorage extends StorageBase
{
    
    protected function executeChain(string $chainname, int $id, $payload = null)
    {
        switch ($chainname) {
            case 'load':
                $this->entities = $this->caller->storage_values;
                break;
            case 'insert':
                if (isset($this->entities['attributes'])) {
                    foreach ($this->entities['attributes'] as $attribute) {
                        $this->entities['attributes'][$attribute['name']]['value_id'] = 9;
                    }
                }
            case 'update':
                $this->caller->storage_values = $this->entities;
                break;
        }
        return 1;
    }
    
    public function executeNeedIDQueries()
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
    
    protected static function setupProperties()
    {
        parent::setupProperties();
        self::integer('intvalue');
        self::object('objectvalue')->setAllowedObjects(['dummy'])->setDefault(null);
        self::arrayofstrings('sarray');
        self::arrayOfObjects('oarray')->setAllowedObjects(['dummy']);
        self::calculated('calcvalue');
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

