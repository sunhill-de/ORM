<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

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
    
    /**
     * @group loadcollection
     */
    public function testLoadComplexCollection()
    {
        $test = new ComplexCollection();
        
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
    
    /**
     * @group loadcollection
     */
    public function testLoadEmptyComplexCollection()
    {
        $test = new ComplexCollection();
        
        $test->load(12);
        
        $this->assertEquals(123,$test->field_int);
        $this->assertEquals('EEE',$test->field_char);
        $this->assertEquals(1.23,$test->field_float);
        $this->assertEquals('eirmod tempor invidunt ut labore',$test->field_text);
        $this->assertEquals('2013-11-24 01:10:00',$test->field_datetime);
        $this->assertEquals('2013-11-24',$test->field_date);
        $this->assertEquals('01:10:00',$test->field_time);
        $this->assertEquals('testA',$test->field_enum);
        $this->assertEquals(4,$test->field_object->getID());
        $this->assertEquals('123A',$test->field_calc);
        $this->assertTrue(empty($test->field_oarray));
        $this->assertTrue(empty($test->field_sarray));
        $this->assertTrue(empty($test->field_smap));
    }
    
}