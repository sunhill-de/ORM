<?php

namespace Sunhill\ORM\Tests\Utils;

use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Collection;

class TestStorage extends StorageBase
{
    
    public $last_action;
    
    public function dispatch(string $action, $additonal = null)
    {
        if (is_a($this->getCollection(), ORMObject::class)) {
            $this->last_action = 'object_';
        } else if (is_a($this->getCollection(), Collection::class)) {
            $this->last_action = 'collection_';
        }
            
        $this->last_action .= $action;
    }
}