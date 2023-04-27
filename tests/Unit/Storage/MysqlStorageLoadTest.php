<?php

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class MysqlStorageLoadTest extends DatabaseTestCase
{
    
    public function testDummy()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        
        $test->load(1);
        $this->assertEquals(123,$test->dummyint);
        $this->assertEquals('a123',$test->uuid);
        $this->assertEquals('2019-05-15 10:00:00',$test->created_at);
        $this->assertEquals('general_attribute',$test->attributes[0]->name);
        $this->assertEquals(444,$test->attributes[0]->value);
    }

    public function testDummyChildLittleMoreComplexLoad()
    {
        $object = new DummyChild();
        $test = new MysqlStorage($object);
        
        $test->load(8);
        $this->assertEquals(999,$test->dummychildint);
        $this->assertEquals(789,$test->dummyint);
    }
    
    public function testTestParent()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        
        $test->load(9);
        $this->assertEquals(111,$test->parentint);
        $this->assertEquals('ABC',$test->parentchar);
        $this->assertEquals(1.11,$test->parentfloat);
        $this->assertEquals('Lorem ipsum',$test->parenttext);
        $this->assertEquals('1974-09-15 17:45:00',$test->parentdatetime);
        $this->assertEquals('1974-09-15',$test->parentdate);
        $this->assertEquals('17:45:00',$test->parenttime);
        $this->assertEquals('testC',$test->parentenum);
        $this->assertEquals(1,$test->parentobject);
        $this->assertEquals(['String A','String B'],$test->parentsarray);
        $this->assertEquals([2,3],$test->parentoarray);
        $this->assertEquals('111A',$test->parentcalc);
        $this->assertEquals([3,4,5],$test->tags);
        $this->assertEquals('attribute1',$test->attributes[0]->name);
        $this->assertEquals(123,$test->attributes[0]->value);
        $this->assertEquals('attribute2',$test->attributes[1]->name);
        $this->assertEquals(222,$test->attributes[1]->value);
        $this->assertEquals('111A',$test->parentcalc);
    }
    
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage($object);
        
        $test->load(18);
        $this->assertEquals(800,$test->parentint);
        $this->assertEquals('DEF',$test->parentchar);
        $this->assertEquals(8,$test->parentfloat);
        $this->assertEquals('no sea takimata sanctus',$test->parenttext);
        $this->assertEquals('1974-09-15 17:45:00',$test->parentdatetime);
        $this->assertEquals('1974-09-15',$test->parentdate);
        $this->assertEquals('17:45:00',$test->parenttime);
        $this->assertEquals('testB',$test->parentenum);
        $this->assertEquals(4,$test->parentobject);
        $this->assertEquals(['Something','Something else','Another something'],$test->parentsarray);
        $this->assertEquals([3,2,1],$test->parentoarray);
        $this->assertEquals('800A',$test->parentcalc);
        
        $this->assertEquals(801,$test->childint);
        $this->assertEquals('DEF',$test->childchar);
        $this->assertEquals(8,$test->childfloat);
        $this->assertEquals('no sea takimata sanctus',$test->childtext);
        $this->assertEquals('1974-09-15 17:45:00',$test->childdatetime);
        $this->assertEquals('1974-09-15',$test->childdate);
        $this->assertEquals('17:45:00',$test->childtime);
        $this->assertEquals('testB',$test->childenum);
        $this->assertEquals(4,$test->childobject);
        $this->assertEquals(['Yea','Yupp'],$test->childsarray);
        $this->assertEquals([5,6,7],$test->childoarray);
        $this->assertEquals('801B',$test->childcalc);
    }
}