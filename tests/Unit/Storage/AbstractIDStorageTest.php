<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Storage\AbstractIDStorage;

class TestAbstractIDStorage extends AbstractIDStorage
{
    
    public $data = [['test_str'=>'ABC','test_int'=>123],['test_str'=>'DEF','test_int'=>345]];
    
    public $next_id = 2;
    
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
    
    protected function readFromID(int $id)
    {
        $this->values = $this->data[$id];
    }
    
    protected function writeToID(): int
    {
        $this->data[$this->next_id] = $this->values;
        return $this->next_id++;
    }
    
    protected function updateToID(int $id)
    {
        $this->data[$id] = $this->values;        
    }
        
}


class AbstractIDStorageTest extends TestCase
{

    public function testLoadFromStorage()
    {
        $test = new TestAbstractIDStorage();
        $test->setID(1);
        $this->assertEquals('DEF', $test->getValue('test_str'));
        $this->assertFalse($test->isDirty());
    }
    
    public function testStoreNewEntry()
    {
        $test = new TestAbstractIDStorage();
        $test->setValue('test_str','AAA');
        $test->setValue('test_int',111);
        $test->commit();
        $this->assertEquals(2,$test->getID());
        $this->assertEquals('AAA',$test->data[2]['test_str']);
    }
    
    public function testUpdateEntry()
    {
        $test = new TestAbstractIDStorage();
        $test->setID(1);
        $test->setValue('test_str','AAA');
        $test->setValue('test_int',111);
        $test->commit();
        $this->assertEquals(1, $test->getID());
        $this->assertEquals('AAA', $test->data[1]['test_str']);
    }
}