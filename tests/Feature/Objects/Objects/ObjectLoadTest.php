<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Properties\Exceptions\AttributeException;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;

class ObjectLoadTest extends DatabaseTestCase
{

    public function testLoadDummy()
    {
        $test = new Dummy();
        $test->load(1);
        $this->assertEquals(123,$test->dummyint);
        $this->assertEquals(444,$test->general_attribute);
        $this->assertTrue($test->tags->hasTag('TagA'));
    }
    
    public function testLoadTestParent()
    {
        $test = new TestParent();
        $test->load(9);
        $this->assertEquals(111, $test->parentint);
        $this->assertEquals('ABC', $test->parentchar);
        $this->assertEquals(true, $test->parentbool);
        $this->assertEquals(1.11, $test->parentfloat);
        $this->assertEquals('Lorem ipsum', $test->parenttext);
        $this->assertEquals('1974-09-15', $test->parentdate);
        $this->assertEquals('17:45:00', $test->parenttime);
        $this->assertEquals('1974-09-15 17:45:00', $test->parentdatetime);
        $this->assertEquals(123, $test->parentobject->dummyint);
        $this->assertEquals('111A', $test->parentcalc);
        $this->assertEquals(678, $test->parentcollection->dummyint);
        $this->assertEquals(234, $test->parentoarray[0]->dummyint);
        $this->assertEquals('String B', $test->parentsarray[1]);
        $this->assertEquals('Value B', $test->parentmap['KeyB']);
        $this->assertTrue($test->tags->hasTag('TagC'));
        $this->assertEquals(222, $test->attribute2);
    }
    
    public function testLoadTestChild()
    {
        $test = new TestChild();
        $test->load(17);
        $this->assertEquals(123, $test->parentint);
        $this->assertEquals('RRR', $test->parentchar);
        $this->assertEquals(true, $test->parentbool);
        $this->assertEquals(1.23, $test->parentfloat);
        $this->assertEquals('amet. Lorem ipsum dolo', $test->parenttext);
        $this->assertEquals('1978-06-05', $test->parentdate);
        $this->assertEquals('11:45:00', $test->parenttime);
        $this->assertEquals('1978-06-05 11:45:00', $test->parentdatetime);
        $this->assertEquals(123, $test->parentobject->dummyint);
        $this->assertEquals('123A', $test->parentcalc);
        $this->assertEquals(456, $test->parentcollection->dummyint);
        $this->assertEquals(456, $test->parentoarray[0]->dummyint);
        $this->assertEquals('ABCDEFG', $test->parentsarray[0]);
        $this->assertEquals('DEF', $test->parentmap['KeyC']);
        $this->assertTrue($test->tags->hasTag('TagB'));
        $this->assertEquals(543, $test->attribute2);
        
        $this->assertEquals(777, $test->childint);
        $this->assertEquals('WWW', $test->childchar);
        $this->assertEquals(1.23, $test->childfloat);
        $this->assertEquals('amet. Lorem ipsum dolo', $test->childtext);
        $this->assertEquals('1978-06-05', $test->childdate);
        $this->assertEquals('11:45:00', $test->childtime);
        $this->assertEquals('testC', $test->childenum);
        $this->assertEquals('1978-06-05 11:45:00', $test->childdatetime);
        $this->assertEquals(123, $test->childobject->dummyint);
        $this->assertEquals('777B', $test->childcalc);
        $this->assertEquals(111, $test->childcollection->field_int);
        $this->assertEquals('VXYZABC', $test->childsarray[1]);        
    }
    
    public function testLoadDummyChild()
    {
        $test = new DummyChild();
        $test->load(5);
        $this->assertEquals(123, $test->dummyint);
        $this->assertEquals(123, $test->dummychildint);
    }
    
    public function testLoadReferenceOnly()
    {
        $test = new ReferenceOnly();
        $test->load(27);
        
        $this->assertEquals('Test B', $test->testsarray[1]);
        $this->assertEquals(123,$test->testoarray[1]->dummyint);
    }
    
    public function testLoadSecondLevelChild()
    {
        $test = new SecondLevelChild();
        $test->load(32);
        $this->assertEquals(1, $test->childint);
    }
 
    public function testLoadSimpleChild()
    {
        $test = new TestSimpleChild();
        $test->load(26);

        $this->assertEquals(123, $test->parentint);
        $this->assertEquals(null, $test->parentchar);
        $this->assertEquals(1,$test->parentbool);
        $this->assertEquals(1.23,$test->parentfloat);
        $this->assertEquals('Lorem ipsum dolor sit amet, consetetur sadipscing',$test->parenttext);
        $this->assertEquals('1999-12-31 23:59:59', $test->parentdatetime);
        $this->assertEquals('1999-12-31', $test->parentdate);
        $this->assertEquals('23:59:59',$test->parenttime);
        $this->assertEquals('testB',$test->parentenum);
        $this->assertEquals(null, $test->parentobject);
        $this->assertEquals('123A',$test->parentcalc);
        $this->assertEquals(234,$test->parentcollection->dummyint);
        
    }
}
