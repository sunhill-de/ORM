<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

class ObjectInsertTest extends DatabaseTestCase
{

    protected function getDummy($id)
    {
        $dummy = new Dummy();
        $dummy->load($id);
        return $dummy;
    }
    
    protected function getCollection($id)
    {
        $dummy = new DummyCollection();
        $dummy->load($id);
        return $dummy;
    }
    
    protected function getComplexCollection($id)
    {
        $test = new ComplexCollection();
        $test->load($id);
        return $test;
    }
    
    public function testInsertDummy()
    {
        $test = new Dummy();
        $test->dummyint = 919;
        $test->tags->stick('TagB');
        $test->general_attribute = 3;
        $test->commit();
        
        $load = Objects::load($test->getID());
        $this->assertEquals(919, $load->dummyint);
        $this->assertEquals(3, $load->general_attribute);
        $this->assertTrue($load->tags->hasTag('TagB'));
    }
    
    public function testInsertTestParent()
    {
        $test = new TestParent();
        $test->parentint = 842;
        $test->parentchar = null;
        $test->parentbool = true;
        $test->parentfloat = 3.14;
        $test->parenttext = 'I needed time to think to get those memories from my mind';
        $test->parentdatetime = '2023-08-24 12:37:30';        
        $test->parentdate = '2023-08-24';
        $test->parenttime = '12:37:30';
        $test->parentenum = 'testA';
        $test->parentobject = $this->getDummy(1);
        $test->parentcollection = $this->getCollection(1);
        $test->parentoarray[] = $this->getDummy(2);
        $test->parentoarray[] = $this->getDummy(3);
        $test->parentsarray[] = 'Iron Maiden';
        $test->parentsarray[] = 'Def Leppard';
        $test->parentmap['KeyA'] = 'Value A';
        $test->parentmap['KeyC'] = 'Value C';
        $test->attribute2 = 123;
        $test->tags->stick('TagA');
        $test->tags->stick('TagB');
        $test->commit();

        $load = Objects::load($test->getID());
        $this->assertEquals(842, $load->parentint);
        $this->assertEquals(null, $load->parentchar);
        $this->assertEquals(1, $load->parentbool);
        $this->assertEquals(3.14, $load->parentfloat);        
        $this->assertEquals('I needed time to think to get those memories from my mind', $load->parenttext); 
        $this->assertEquals('2023-08-24 12:37:30', $load->parentdatetime);
        $this->assertEquals('2023-08-24', $load->parentdate);
        $this->assertEquals('12:37:30', $load->parenttime);
        $this->assertEquals(123, $load->parentobject->dummyint);
        $this->assertEquals(123, $load->parentcollection->dummyint);
        $this->assertEquals(234, $load->parentoarray[0]->dummyint);
        $this->assertEquals('Def Leppard', $load->parentsarray[1]);        
        $this->assertEquals('Value C', $load->parentmap['KeyC']);
        $this->assertEquals(123, $load->attribute2);
        $this->assertTrue($load->tags->hasTag('TagB'));
    }
    
    public function testInsertTestChild()
    {
        $test = new TestChild();
        $test->parentint = 842;
        $test->parentchar = null;
        $test->parentbool = true;
        $test->parentfloat = 3.14;
        $test->parenttext = 'I needed time to think to get those memories from my mind';
        $test->parentdatetime = '2023-08-24 12:37:30';
        $test->parentdate = '2023-08-24';
        $test->parentenum = 'testA';
        $test->parenttime = '12:37:30';
        $test->parentobject = $this->getDummy(1);
        $test->parentcollection = $this->getCollection(1);
        $test->parentoarray[] = $this->getDummy(2);
        $test->parentoarray[] = $this->getDummy(3);
        $test->parentsarray[] = 'Iron Maiden';
        $test->parentsarray[] = 'Def Leppard';
        $test->parentmap['KeyA'] = 'Value A';
        $test->parentmap['KeyC'] = 'Value C';
        
        $test->childint = 248;
        $test->childchar = 'AAC';
        $test->childfloat = 1.41;
        $test->childenum = 'testC';
        $test->childtext = 'Why so sad my valentine?';
        $test->childdatetime = '2023-08-24 12:37:30';
        $test->childdate = '2023-08-24';
        $test->childtime = '12:37:30';
        $test->childobject = $this->getDummy(2);
        $test->childcollection = $this->getComplexCollection(9);
        $test->childoarray[] = $this->getDummy(5);
        $test->childoarray[] = $this->getDummy(6);
        $test->childsarray[] = 'Muse';
        $test->childsarray[] = 'Radiohead';
        $test->childmap['KeyA'] = $this->getDummy(1);
        $test->childmap['KeyC'] = $this->getDummy(2);
        
        $test->attribute2 = 234;
        $test->tags->stick('TagC');
        $test->tags->stick('TagD');
        $test->commit();
 
        $load = Objects::load($test->getID());
        $this->assertEquals(842, $load->parentint);
        $this->assertEquals(null, $load->parentchar);
        $this->assertEquals(1, $load->parentbool);
        $this->assertEquals('testA', $load->parentenum);
        $this->assertEquals(3.14, $load->parentfloat);
        $this->assertEquals('I needed time to think to get those memories from my mind', $load->parenttext);
        $this->assertEquals('2023-08-24 12:37:30', $load->parentdatetime);
        $this->assertEquals('2023-08-24', $load->parentdate);
        $this->assertEquals('12:37:30', $load->parenttime);
        $this->assertEquals(123, $load->parentobject->dummyint);
        $this->assertEquals(123, $load->parentcollection->dummyint);
        $this->assertEquals(234, $load->parentoarray[0]->dummyint);
        $this->assertEquals('Def Leppard', $load->parentsarray[1]);
        $this->assertEquals('Value C', $load->parentmap['KeyC']);
        
        $this->assertEquals(248, $load->childint);
        $this->assertEquals('AAC', $load->childchar);
        $this->assertEquals('testC', $load->childenum);
        $this->assertEquals(1.41, $load->childfloat);
        $this->assertEquals('Why so sad my valentine?', $load->childtext);
        $this->assertEquals('2023-08-24 12:37:30', $load->childdatetime);
        $this->assertEquals('2023-08-24', $load->childdate);
        $this->assertEquals('12:37:30', $load->childtime);
        $this->assertEquals(234, $load->childobject->dummyint);
        $this->assertEquals(111, $load->childcollection->field_int);
        $this->assertEquals(123, $load->childoarray[0]->dummyint);
        $this->assertEquals('Muse', $load->childsarray[0]);
        $this->assertEquals(234, $load->childmap['KeyC']->dummyint);
        
        $this->assertEquals(234, $load->attribute2);
        $this->assertTrue($load->tags->hasTag('TagC'));
        
    }
    
    public function testLoadDummyChild()
    {
        $test = new DummyChild();
        $test->dummyint = 1509;
        $test->dummychildint = 2411;
        $test->commit();
        
        $load = Objects::load($test->getID());
        $this->assertEquals(1509, $load->dummyint);
        $this->assertEquals(2411, $load->dummychildint);
    }
    
    public function testLoadReferenceOnly()
    {
        $test = new ReferenceOnly();
        $test->testsarray[] = 'Muzzle';
        $test->testsarray[] = 'Bruce Springsteen';
        $test->testoarray[] = $this->getDummy(2);
        $test->testoarray[] = $this->getDummy(3);
        
        $test->commit();
        
        $load = Objects::load($test->getID());
        $this->assertEquals('Bruce Springsteen', $load->testsarray[1]);
        $this->assertEquals(234, $load->testoarray[0]->dummyint);
    }
    
    public function testLoadSecondLevelChild()
    {
        $test = new SecondLevelChild();
        $test->testsarray[] = 'Muzzle';
        $test->testsarray[] = 'Bruce Springsteen';
        $test->testoarray[] = $this->getDummy(2);
        $test->testoarray[] = $this->getDummy(3);
        $test->childint = 2411;
    
        $test->commit();
    
        $load = Objects::load($test->getID());
        $this->assertEquals('Bruce Springsteen', $load->testsarray[1]);
        $this->assertEquals(234, $load->testoarray[0]->dummyint);
        $this->assertEquals(2411, $load->childint);
    }
    
    public function testLoadSimpleChild()
    {
        $test = new TestSimpleChild();
        $test->parentint = 842;
        $test->parentchar = null;
        $test->parentbool = true;
        $test->parentfloat = 3.14;
        $test->parenttext = 'I needed time to think to get those memories from my mind';
        $test->parentdatetime = '2023-08-24 12:37:30';
        $test->parentdate = '2023-08-24';
        $test->parenttime = '12:37:30';
        $test->parentenum = 'testA';
        $test->parentobject = $this->getDummy(1);
        $test->parentcollection = $this->getCollection(1);
        $test->parentoarray[] = $this->getDummy(2);
        $test->parentoarray[] = $this->getDummy(3);
        $test->parentsarray[] = 'Iron Maiden';
        $test->parentsarray[] = 'Def Leppard';
        $test->parentmap['KeyA'] = 'Value A';
        $test->parentmap['KeyC'] = 'Value C';
        $test->attribute2 = 123;
        $test->tags->stick('TagA');
        $test->tags->stick('TagB');
        $test->commit();
        
        $load = Objects::load($test->getID());
        $this->assertEquals(842, $load->parentint);
        $this->assertEquals(null, $load->parentchar);
        $this->assertEquals(1, $load->parentbool);
        $this->assertEquals(3.14, $load->parentfloat);
        $this->assertEquals('I needed time to think to get those memories from my mind', $load->parenttext);
        $this->assertEquals('2023-08-24 12:37:30', $load->parentdatetime);
        $this->assertEquals('2023-08-24', $load->parentdate);
        $this->assertEquals('12:37:30', $load->parenttime);
        $this->assertEquals(123, $load->parentobject->dummyint);
        $this->assertEquals(123, $load->parentcollection->dummyint);
        $this->assertEquals(234, $load->parentoarray[0]->dummyint);
        $this->assertEquals('Def Leppard', $load->parentsarray[1]);
        $this->assertEquals('Value C', $load->parentmap['KeyC']);
        $this->assertEquals(123, $load->attribute2);
        $this->assertTrue($load->tags->hasTag('TagB'));
        
    }
    
}
