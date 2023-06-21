<?php

namespace Sunhill\ORM\Tests\Utils;

use Sunhill\ORM\Storage\StorageBase;

class TestStorage extends StorageBase
{
    public $last_action = '';
    
    protected $values = [];
    
    public function setValue($key, $value)
    {
        $this->values[$key] = $value;    
    }
    
    protected function doLoad(int $id)
    {
        $this->last_action = 'load';
        foreach ($this->values as $key => $value) {            
            if ($key == 'attributes') {
                $this->entities['attributes'] = $value;
            } else {
                $this->getEntity($key)->setValue($value);
            }
        }
    }
    
    protected function doStore(): int
    {
        $this->last_action = 'store';        
    }
        
    protected function doUpdate(int $id)
    {
        $this->last_action = 'update';        
    }
        
    protected function doDelete(int $id)
    {
        $this->last_action = 'delete';        
    }
        
    protected function doMigrate()
    {
        $this->last_action = 'migrate';        
    }
        
    protected function doPromote()
    {
        $this->last_action = 'promote';        
    }
        
    protected function doDegrade()
    {
        $this->last_action = 'degrade';        
    }
        
    protected function doSearch()
    {
        $this->last_action = 'search';        
    }
        
    protected function doDrop()
    {
        $this->last_action = 'drop';        
    }
    
    
}