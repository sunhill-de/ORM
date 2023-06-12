<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

class LoadTest extends CollectionBase
{
    
    public function testLoadDummyCollection()
    {
        $test = $this->getDummyStorage();
        
        $test->load(1);
        
        $this->assertEquals(123,$test->dummyint);
    }
    
    public function testLoadComplexCollection()
    {
        $test = $this->getComplexStorage();
        
        $test->load(9);
        
        $this->assertEquals(111,$test->field_int);        
        $this->assertEquals('ABC',$test->field_char);        
        $this->assertEquals(1.11,$test->field_float);
        $this->assertEquals('Lorem ipsum',$test->field_text);
        $this->assertEquals('1974-09-15 17:45:00',$test->field_datetime);
        $this->assertEquals('1974-09-15',$test->field_date);
        $this->assertEquals('17:45:00',$test->field_time);
        $this->assertEquals('testC',$test->field_enum);
        $this->assertEquals(1,$test->field_object);
        $this->assertEquals('111A',$test->field_calc);        
        $this->assertEquals(2,$test->field_oarray[0]);
        $this->assertEquals('String B',$test->field_sarray[1]);
        $this->assertEquals('ValueB',$test->field_smap['KeyB']);
    }
        
}