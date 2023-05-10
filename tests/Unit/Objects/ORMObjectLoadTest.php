<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;

class ORMObjectLoadTest extends TestCase
{

    public function testDummy()
    {
        $test = new Dummy();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        
        $test->load(1);
        
        $this->assertEquals(123,$test->dummyint);
        $this->assertEquals('2023-05-05 10:00:00', $test->created_at);
        $this->assertEquals(0, $test->obj_owner);
    }
    
    public function testDummyTags()
    {
        $test = new Dummy();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        
        $test->load(1);
        
        $this->assertEquals(1,$test->tags[0]->getID());        
    }
    
    public function testLazyLoading()
    {
        $test = new Dummy();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        
        $test->load(1);
    
        $this->assertEquals(1, $test->getID());
        $property = $this->getProtectedProperty($test, 'properties')['dummyint'];
        $this->assertNull($this->getProtectedProperty($property,'value'));
        $this->assertEquals(123,$test->dummyint);
        $this->assertEquals(123,$this->getProtectedProperty($property,'value'));
    }
    
    public function testTestParent()
    {
        $test = new TestParent();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        
        $test->load(9);
        
        $this->assertEquals('ABC',$test->parentchar);
        $this->assertEquals(123,$test->parentint);
        $this->assertEquals(1.23,$test->parentfloat);
    }
}