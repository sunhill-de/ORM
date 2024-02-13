<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Storage\AbstractStorage;

class TestAbstractStorage extends AbstractStorage
{
    protected $values = ['test'=>'TESTVALUE'];
    
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
    
    protected function doSetValue(string $name, $value)
    {
        $this->values[$name] = $value;        
    }
  
    public function isDirty(): bool
    {
        return false;
    }
    
}

class AbstractStorageTest extends TestCase
{

    public function testReadValue()
    {
        $test = new TestAbstractStorage();
        $this->assertEquals('TESTVALUE', $test->getValue('test'));
    }
    
    public function testWriteValue()
    {
        $test = new TestAbstractStorage();
        $test->setValue('new','NEWVALUE');
        $this->assertEquals('NEWVALUE', $test->getValue('new'));
    }
    
    public function testUpdateValue()
    {
        $test = new TestAbstractStorage();
        $test->setValue('test', 'NEWVALUE');
        $this->assertEquals('NEWVALUE', $test->getValue('test'));
    }
}