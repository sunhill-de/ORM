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

class ObjectSeedTest extends DatabaseTestCase
{

    public function testSeedDummy()
    {
        $id = Dummy::seed([
            ['dummyint'=>112],
            ['dummyint'=>919]
        ]);
                
        $load = Objects::load($id);
        $this->assertEquals(919, $load->dummyint);
    }
    
    public function testPostSeedDummy()
    {
        Dummy::postSeed([1=>['dummyint'=>987]]);
        
        $load = Objects::load(1);
        $this->assertEquals(987, $load->dummyint);
    }
    
    public function testSeedTestParent()
    {
        $id = TestParent::seed([
            [
            'parentint'=>842,
            'parentchar'=>null,
            'parentbool'=>true,
            'parentfloat'=>3.14,
            'parenttext'=>'I needed time to think to get those memories from my mind',
            'parentdatetime'=>'2023-08-24 12:37:30',        
            'parentdate'=>'2023-08-24',
            'parenttime'=>'12:37:30',
            'parentenum'=>'testA',
            'parentobject'=>1,
            'parentcollection'=>1,
            'parentoarray'=>[2,3],
            'parentsarray'=>['Iron Maiden','Def Leppard'],
            'parentmap'=>['KeyA'=>'Value A','KeyC'=>'Value C'],
            'attribute2'=>123,
            ]
        ]);

        $load = Objects::load($id);
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
    }
    
    public function testPostSeedTestParent()
    {
        TestParent::postSeed([17=>['parentobject'=>5]]);
        
        $load = Objects::load(17);
        $this->assertEquals(5, $load->parentobject->getID());
    }
    
    public function testInsertTestChild()
    {
        $id = TestChild::seed([
            [
                'parentint'=>842,
                'parentchar'=>null,
                'parentbool'=>true,
                'parentfloat'=>3.14,
                'parenttext'=>'I needed time to think to get those memories from my mind',
                'parentdatetime'=>'2023-08-24 12:37:30',
                'parentdate'=>'2023-08-24',
                'parentenum'=>'testA',
                'parenttime'=>'12:37:30',
                'parentobject'=>1,
                'parentcollection'=>1,
                'parentoarray'=>[2,3],
                'parentsarray'=>['Iron Maiden','Def Leppard'],
                'parentmap'=>['KeyA'=>'Value A','KeyC'=>'Value C'],        
                'childint'=>248,
                'childchar'=>'AAC',
                'childfloat'=>1.41,
                'childenum'=>'testC',
                'childtext'=>'Why so sad my valentine?',
                'childdatetime'=>'2023-08-24 12:37:30',
                'childdate'=>'2023-08-24',
                'childtime'=>'12:37:30',
                'childobject'=>2,
                'childcollection'=>9,
                'childoarray'=>[5,6],
                'childsarray'=>['Muse','Radiohead'],
                'childmap'=>['KeyA'=>1, 'KeyB'=>2],        
                'attribute2'=>234,
            ]            
        ]);
        $load = Objects::load($id);
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
        $this->assertEquals(234, $load->childmap['KeyB']->dummyint);
        
        $this->assertEquals(234, $load->attribute2);
        
    }
    
    public function testLoadDummyChild()
    {
        $id = DummyChild::seed([
            [
                'dummyint'=>1509,
                'dummychildint'=>2411,                
            ]
        ]);        
        $load = Objects::load($id);
        $this->assertEquals(1509, $load->dummyint);
        $this->assertEquals(2411, $load->dummychildint);
    }
    
    public function testLoadReferenceOnly()
    {
        $id = ReferenceOnly::seed([
            [
                'testsarray'=>['Muzzle','Bruce Springsteen'],
                'testoarray'=>[2,3]
            ]                
        ]);
        
        $load = Objects::load($id);
        $this->assertEquals('Bruce Springsteen', $load->testsarray[1]);
        $this->assertEquals(234, $load->testoarray[0]->dummyint);
    }
    
    public function testLoadSecondLevelChild()
    {
        $id = SecondLevelChild::seed([
            [
                'testsarray'=>['Muzzle','Bruce Springsteen'],
                'testoarray'=>[2,3],
                'childint'=>2411                
            ]
        ]);
    
        $load = Objects::load($id);
        $this->assertEquals('Bruce Springsteen', $load->testsarray[1]);
        $this->assertEquals(234, $load->testoarray[0]->dummyint);
        $this->assertEquals(2411, $load->childint);
    }
    
    public function testLoadSimpleChild()
    {
        $id = TestSimpleChild::seed([
            [
                'parentint'=>842,
                'parentchar'=>null,
                'parentbool'=>true,
                'parentfloat'=>3.14,
                'parenttext'=>'I needed time to think to get those memories from my mind',
                'parentdatetime'=>'2023-08-24 12:37:30',
                'parentdate'=>'2023-08-24',
                'parenttime'=>'12:37:30',
                'parentenum'=>'testA',
                'parentobject'=>1,
                'parentcollection'=>1,
                'parentoarray'=>[2,3],
                'parentsarray'=>['Iron Maiden','Def Leppard'],
                'parentmap'=>['KeyA'=>'Value A','KeyC'=>'Value C'],
                'attribute2'=>123,
            ]
        ]);
        
        $load = Objects::load($id);
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
    }
    
}
