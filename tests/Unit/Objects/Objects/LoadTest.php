<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Tests\Utils\TestStorage;

use Sunhill\ORM\Properties\PropertyTags;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Unit\CommonStorage\DummyLoadStorage;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Tests\Unit\CommonStorage\TestParentLoadStorage;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Unit\CommonStorage\TestChildtLoadStorage;
use Sunhill\ORM\Tests\Unit\CommonStorage\TestChildLoadStorage;


/**
 * @group loadobject
 * @group load
 * @author klaus
 */
class LoadTest extends TestCase
{
    
    public function testDummyPreloading()
    {
        Classes::registerClass(Dummy::class);
        
        $test = new Dummy();
        $storage = new TestStorage();
        
        $this->callProtectedMethod($test, 'prepareStorage',[$storage]);
        
        $expected_storage = new TestStorage();
        $expected_storage->createEntity('dummyint','dummies')->setType(PropertyInteger::class);
        $expected_storage->createEntity('_uuid','objects')->setType(PropertyVarchar::class);
        $expected_storage->createEntity('_created_at','objects')->setType(PropertyDatetime::class);
        $expected_storage->createEntity('_updated_at','objects')->setType(PropertyDatetime::class);
        $expected_storage->createEntity('_owner','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('_group','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('_read','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('_edit','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('_delete','objects')->setType(PropertyInteger::class);
        
        $expected_storage->assertStorageEquals($storage);        
    }
    
    public function testTestparentPreloading()
    {
        Classes::registerClass(TestParent::class);
        Classes::registerClass(Dummy::class);
        
        $test = new Testparent();
        $storage = new TestStorage();
        
        $this->callProtectedMethod($test, 'prepareStorage',[$storage]);
        
        $expected_storage = new TestStorage();
        $expected_storage->createEntity('parentint','testparents')->setType(PropertyInteger::class);
        $expected_storage->createEntity('parentchar','testparents')->setType(PropertyVarchar::class);
        $expected_storage->createEntity('parentoarray','testparents')->setType(PropertyArray::class)->setElementType(PropertyObject::class);
        
        $expected_storage->createEntity('tags','objects')->setType(PropertyTags::class);        
        $expected_storage->createEntity('_uuid','objects')->setType(PropertyVarchar::class);
        
        $expected_storage->assertStorageEquals($storage);
    }

    public function testDummyLoad()
    {
        Classes::registerClass(TestParent::class);
        Classes::registerClass(Dummy::class);
        
        $test = new Dummy();
        
        // Prepare the tag
        $tag = new Tag();
        $tag->load(1);
        Tags::shouldReceive('loadTag')->andReturn($tag);
        
        // Prepare the storage
        $storage = new DummyLoadStorage();
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->load(1);
        
        $this->assertEquals(123,$test->dummyint);        
        $this->assertEquals(444, $test->general_attribute);
        $this->assertEquals(1, $test->tags[0]->getID());
    }
    
    protected function expectTag($id)
    {
        $tag = new Tag();
        $tag->load($id);
        Tags::shouldReceive('loadTag')->with($id)->andReturn($tag);
    }
    
    protected function expectObject($id)
    {
        $obj = new ORMObject();
        $this->setProtectedProperty($obj,'id',$id);
        Objects::shouldReceive('load')->with($id)->andReturn($obj);
    }
    
    public function testTestparentLoad()
    {
        Classes::registerClass(TestParent::class);
        Classes::registerClass(Dummy::class);
        
        $test = new TestParent();
        
        $storage = new TestParentLoadStorage();
        
        // Prepare the tag
        $this->expectTag(3);
        $this->expectTag(4);
        $this->expectTag(5);
                
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);

        $this->expectObject(1);
        $this->expectObject(2);
        $this->expectObject(3);
        
        $test->load(9);

        $this->assertEquals(111, $test->parentint);
        $this->assertEquals('ABC',$test->parentchar);
        $this->assertEquals(1.11,$test->parentfloat);
        $this->assertEquals('Lorem ipsum',$test->parenttext);
        $this->assertEquals('1974-09-15 17:45:00',$test->parentdatetime);        
        $this->assertEquals('1974-09-15',$test->parentdate);        
        $this->assertEquals('17:45:00',$test->parenttime);
        $this->assertEquals('testC',$test->parentenum);
        $this->assertEquals('111A',$test->parentcalc);
        $this->assertEquals(1, $test->parentobject->getID());
     //   $this->assertEquals(7, $test->parentcollection->getID());
        $this->assertEquals(3, $test->tags[0]->getID());
        $this->assertEquals('String B',$test->parentsarray[1]);
        $this->assertEquals(2, $test->parentoarray[0]->getID());
        $this->assertEquals('Value A',$test->parentmap['KeyA']);
    }

    public function testTestchildLoad()
    {
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        Classes::registerClass(Dummy::class);
        
        $test = new TestChild();
        
        $storage = new TestChildLoadStorage();
        
        // Prepare the tag
        $this->expectTag(1);
        $this->expectTag(2);
        $this->expectTag(4);
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $this->expectObject(1);
        $this->expectObject(3);
        $this->expectObject(4);
        $this->expectObject(5);
        
        $test->load(17);
        
        $this->assertEquals(123, $test->parentint);
        $this->assertEquals('RRR',$test->parentchar);
        $this->assertEquals(1.23,$test->parentfloat);
        $this->assertEquals('Lorem ipsum dolo',$test->parenttext);
        $this->assertEquals('1978-06-05 11:45:00',$test->parentdatetime);
        $this->assertEquals('1978-06-05',$test->parentdate);
        $this->assertEquals('11:45:00',$test->parenttime);
        $this->assertEquals('testC',$test->parentenum);
        $this->assertEquals('123A',$test->parentcalc);
        $this->assertEquals(3, $test->parentobject->getID());
        //   $this->assertEquals(4, $test->parentcollection->getID());
        $this->assertEquals(777,$test->childint);
        $this->assertEquals('WWW',$test->childchar);
        $this->assertEquals(1.23,$test->childfloat);        
        $this->assertEquals('amet. Lorem ipsum dolo',$test->childtext);        
        $this->assertEquals('1978-06-05 11:45:00',$test->childdatetime);
        $this->assertEquals('1978-06-05',$test->childdate);
        $this->assertEquals('11:45:00',$test->childtime);
        $this->assertEquals('testC',$test->childenum);
        $this->assertEquals(3,$test->childobject->getID());
        $this->assertEquals('777B',$test->childcalc);        
        //$this->setValue(9, $test->childcollection->getID());
        
        $this->assertEquals('HIJKLMN',$test->parentsarray[1]);
        $this->assertEquals(4, $test->parentoarray[0]->getID());
        $this->assertEquals('DEF',$test->parentmap['KeyC']);
        $this->assertEquals('VXYZABC',$test->childsarray[1]);
        $this->assertEquals(3, $test->childoarray[0]->getID());
        $this->assertEquals(4,$test->childmap['KeyC']->getID());
        
        $this->assertEquals(1, $test->tags[0]->getID());
    }
    
    
}