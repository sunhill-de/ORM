<?php

namespace Sunhill\ORM\Tests\TestSupport;

use Sunhill\ORM\Storage\AbstractStorage;

class TestAbstractStorage extends AbstractStorage
{
    public $values = ['test'=>'TESTVALUE'];
    
    public function getReadCapability(string $name): ?string
    {
        return null; // No need to test
    }
    
    public function getIsReadable(string $name): bool
    {
        return true;
    }
    
    protected function doGetValue(string $name)
    {
        return $this->values[$name];
    }
    
    public function getWriteCapability(string $name): ?string
    {
        return null;
    }
    
    public function getWriteable(string $name): bool
    {
        return true;
    }
    
    public function getModifyCapability(string $name): ?string
    {
        return null;
    }
    
    public function getIsWriteable(string $name): bool
    {
        return true;
    }
    
    public function getIsInitialized(string $name): bool
    {
        return true;
    }
    protected function doSetValue(string $name, $value)
    {
        $this->values[$name] = $value;
    }
    
    public function isDirty(): bool
    {
        return false;
    }
    
}

