<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Facades\Objects;
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
        $return = new Dummy();
        $return->load(1);
        
        $test = new TestParent();
        $fake_storage = new DummyStorage($test);
        Storage::shouldReceive('createStorage')->once()->andReturn($fake_storage);
        Objects::shouldReceive('load')->with(1)->andReturn($return);
        Objects::shouldReceive('load')->with(2)->andReturn($return);
        Objects::shouldReceive('load')->with(3)->andReturn($return);
        Objects::shouldReceive('load')->with(4)->andReturn($return);
        
        $test->load(9);
        
        $this->assertEquals('ABC',$test->parentchar);
        $this->assertEquals(123,$test->parentint);
        $this->assertEquals(1.23,$test->parentfloat);
        $this->assertEquals($test->parenttext,'Lorem ipsum');
        $this->assertEquals($test->parentdatetime,'2023-05-10 11:43:00');
        $this->assertEquals($test->parentdate,'2023-05-10');
        $this->assertEquals($test->parenttime,'11:43:00');
        $this->assertEquals($test->parentenum,'testC');
        $this->assertEquals($test->parentobject->getID(),1);
        $this->assertEquals($test->parentsarray,['AAA','BBB','CCC']);
        $this->assertEquals(1,$test->parentoarray[0]->getID());
        $this->assertEquals('123A',$test->parentcalc);
        $this->assertEquals(2,$test->nosearch);
    }
}