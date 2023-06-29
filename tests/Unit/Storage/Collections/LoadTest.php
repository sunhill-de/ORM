<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Objects;

class LoadTest extends DatabaseTestCase
{
    
    /**
     * @group loadcollection
     */
    public function testLoadDummyCollection()
    {
        $test = new DummyCollection();
        
        $test->load(1);
        
        $this->assertEquals(123,$test->dummyint);
    }
    
    protected function fakeObject($class, $id)
    {
        $object = new $class();
        $object->load($id);
    }
    /**
     * @group loadcollection
     */
    public function testLoadComplexCollection()
    {
        $test = new ComplexCollection();
                
        Objects::shouldReceive('load')->with(1)->andReturn($this->fakeObject(Dummy::class, 1));
        Objects::shouldReceive('load')->with(2)->andReturn($this->fakeObject(Dummy::class, 2));
        Objects::shouldReceive('load')->with(3)->andReturn($this->fakeObject(Dummy::class, 3));
        Objects::shouldReceive('load')->with(4)->andReturn($this->fakeObject(Dummy::class, 4));
        
        $test->load(9);
        
        $this->assertEquals(111,$test->field_int);        
        $this->assertEquals('ABC',$test->field_char);        
        $this->assertEquals(1.11,$test->field_float);
        $this->assertEquals('Lorem ipsum',$test->field_text);
        $this->assertEquals('1974-09-15 17:45:00',$test->field_datetime);
        $this->assertEquals('1974-09-15',$test->field_date);
        $this->assertEquals('17:45:00',$test->field_time);
        $this->assertEquals('testC',$test->field_enum);
        $this->assertEquals(1,$test->field_object->getID());
        $this->assertEquals('111A',$test->field_calc);        
        $this->assertEquals(2,$test->field_oarray[0]->getID());
        $this->assertEquals('String B',$test->field_sarray[1]);
        $this->assertEquals('ValueB',$test->field_smap['KeyB']);
    }
        
}