<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Storage;
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


class LoadTest extends TestCase
{
    
    /**
     * @group loadobject
     */
    public function testDummyPreloading()
    {
        Classes::registerClass(Dummy::class);
        
        $test = new Dummy();
        $storage = new TestStorage();
        
        $this->callProtectedMethod($test, 'prepareStorage',[$storage]);
        
        $expected_storage = new TestStorage();
        $expected_storage->createEntity('dummyint','dummies')->setType(PropertyInteger::class);
        $expected_storage->createEntity('uuid','objects')->setType(PropertyVarchar::class);
        $expected_storage->createEntity('created_at','objects')->setType(PropertyDatetime::class);
        $expected_storage->createEntity('updated_at','objects')->setType(PropertyDatetime::class);
        $expected_storage->createEntity('obj_owner','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('obj_group','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('obj_read','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('obj_edit','objects')->setType(PropertyInteger::class);
        $expected_storage->createEntity('obj_delete','objects')->setType(PropertyInteger::class);
        
        $expected_storage->assertStorageEquals($storage);        
    }
    
    /**
     * @group loadobject
     */
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
        $expected_storage->createEntity('uuid','objects')->setType(PropertyVarchar::class);
        
        $expected_storage->assertStorageEquals($storage);
    }

    /**
     * @group loadobject
     */
    public function testDummyLoad()
    {
        Classes::registerClass(TestParent::class);
        Classes::registerClass(Dummy::class);
        
        $test = new Dummy();
        $storage = new TestStorage();
        
        $storage->setValue('dummyint',123);
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->load(1);
        
        $this->assertEquals(123,$test->dummyint);        
    }
    
    /**
     * @group loadobject
     */
    public function testTestparentLoad()
    {
        Classes::registerClass(TestParent::class);
        Classes::registerClass(Dummy::class);
        
        $test = new Testparent();
        $storage = new TestStorage();
        
        $data_list = [
            'parentint'=>123,
            'parentchar'=>'ABC',
            'parentfloat'=>1.23,
            'parentbool'=>true,
            'parentdate'=>'2023-02-02',
            'parentdatetime'=>'2023-02-02 11:11:11',
            'parenttime'=>'11:11:11',
            'parentenum'=>'testA',
            'parentcalc'=>'123A',
          ];        
        foreach ($data_list as $key => $value) {
            $storage->setValue($key, $value);
        }
        $storage->setValue('parentobject',1);
        $storage->setValue('parentoarray',[2,3,4]);
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        $obj1 = new ORMObject();
        $this->setProtectedProperty($obj1, 'id', 1);
        Objects::shouldReceive('load')->with(1)->andReturn($obj1);
        $obj2 = new ORMObject();
        $this->setProtectedProperty($obj2, 'id', 2);
        Objects::shouldReceive('load')->with(2)->andReturn($obj2);
        $obj3 = new ORMObject();
        $this->setProtectedProperty($obj3, 'id', 3);
        Objects::shouldReceive('load')->with(3)->andReturn($obj3);
        $obj4 = new ORMObject();
        $this->setProtectedProperty($obj4, 'id', 4);
        Objects::shouldReceive('load')->with(4)->andReturn($obj4);
        
        $storage->setValue('parentsarray',['ABC','DEF','GHI']);
        
        $test->load(1);
        foreach ($data_list as $key => $value) {
            $this->assertEquals($value, $test->$key);
        }
        $this->assertEquals('DEF',$test->parentsarray[1]);
        $this->assertEquals(3,$test->parentoarray[1]->getID());
    }
    
}