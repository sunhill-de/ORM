<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Collections;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Utils\TestStorage;

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
use Sunhill\ORM\Objects\ORMObject;


class LoadTest extends TestCase
{
    
    /**
     * @group loadcollection
     */
    public function testDummyCollectionPreloading()
    {
        $test = new DummyCollection();
        $storage = new TestStorage();
        
        $this->callProtectedMethod($test, 'prepareStorage',[$storage]);
        
        $expected_storage = new TestStorage();
        $expected_storage->createEntity('dummyint','dummycollections')->setType(PropertyInteger::class);
        
        $expected_storage->assertStorageEquals($storage);        
    }
    
    /**
     * @group loadcollection
     */
    public function testComplexCollectionPreloading()
    {
        $test = new ComplexCollection();
        $storage = new TestStorage();

        $this->callProtectedMethod($test, 'prepareStorage',[$storage]);
        $list = $storage->getEntitiesOfStorageID('complexcollections');
        
        $expected_storage = new TestStorage();
        $expected_storage->createEntity('field_int','complexcollections')->setType(PropertyInteger::class);
        $expected_storage->createEntity('field_char','complexcollections')->setType(PropertyVarchar::class);
        $expected_storage->createEntity('field_float','complexcollections')->setType(PropertyFloat::class);
        $expected_storage->createEntity('field_text','complexcollections')->setType(PropertyText::class);
        $expected_storage->createEntity('field_date','complexcollections')->setType(PropertyDate::class);
        $expected_storage->createEntity('field_time','complexcollections')->setType(PropertyTime::class);
        $expected_storage->createEntity('field_datetime','complexcollections')->setType(PropertyDatetime::class);
        $expected_storage->createEntity('field_enum','complexcollections')->setType(PropertyEnum::class);
        $expected_storage->createEntity('field_oarray','complexcollections')->setType(PropertyArray::class)->setElementType(PropertyObject::class);
        $expected_storage->createEntity('field_sarray','complexcollections')->setType(PropertyArray::class)->setElementType(PropertyVarchar::class);
        $expected_storage->createEntity('field_smap','complexcollections')->setType(PropertyMap::class)->setElementType(PropertyVarchar::class);
        
        $expected_storage->assertStorageEquals($storage);
    }
    
    /**
     * @group loadcollection
     */
    public function testDummyCollectionLoading()
    {
        $test = new DummyCollection();
        $storage = new TestStorage();
        $storage->setValue('dummyint',123);
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->load(1);
        
        $this->assertEquals(123, $test->dummyint);
    }
    
    /**
     * @group loadcollection
     */
    public function testComplexCollectionLoading()
    {
        $test = new ComplexCollection();
        $storage = new TestStorage(ComplexCollection::class);
        
        $storage->setValue('field_int',123);
        $storage->setValue('field_char','ABC');
        $storage->setValue('field_float',1.23);
        $storage->setValue('field_text','Lorem ipsum');
        $storage->setValue('field_datetime', '2023-05-10 11:43:00');
        $storage->setValue('field_date', '2023-05-10');
        $storage->setValue('field_time', '11:43:00');
        $storage->setValue('field_enum', 'testC');
        $storage->setValue('field_object', 1);
        $storage->setValue('field_oarray', [2,3,4]);
        $storage->setValue('field_sarray', ['AAA','BBB','CCC']);
        $storage->setValue('field_smap', ['KeyA'=>'ValueA','KeyB'=>'ValueB']);
        $storage->setValue('field_calc', '123A');
        
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
        
        $test->load(1);
        
        $this->assertEquals(123,$test->field_int);
        $this->assertEquals('ABC',$test->field_char);
        $this->assertEquals(1.23,$test->field_float);
        $this->assertEquals('Lorem ipsum',$test->field_text);
        $this->assertEquals('2023-05-10 11:43:00',$test->field_datetime);
        $this->assertEquals('2023-05-10',$test->field_date);
        $this->assertEquals('11:43:00',$test->field_time);
        $this->assertEquals('testC',$test->field_enum);
        $this->assertEquals(1,$test->field_object->getID());
        $this->assertEquals('123A',$test->field_calc);
        $this->assertEquals(2,$test->field_oarray[0]->getID());
        $this->assertEquals('BBB',$test->field_sarray[1]);
        $this->assertEquals('ValueB',$test->field_smap['KeyB']);        
    }

    /**
     * @group loadcollection
     */
    public function testComplexEmptyCollectionLoading()
    {
        $test = new ComplexCollection();
        $storage = new TestStorage(ComplexCollection::class.'empty');
        
        $storage->setValue('field_char','ABC');
        $storage->setValue('field_float',1.23);
        $storage->setValue('field_text','Lorem ipsum');
        $storage->setValue('field_datetime', '2023-05-10 11:43:00');
        $storage->setValue('field_date', '2023-05-10');
        $storage->setValue('field_time', '11:43:00');
        $storage->setValue('field_enum', 'testC');
        $storage->setValue('field_calc', '123A');
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->load(1);
        
        $this->assertEquals(null,$test->field_int);
        $this->assertEquals('ABC',$test->field_char);
        $this->assertEquals(1.23,$test->field_float);
        $this->assertEquals('Lorem ipsum',$test->field_text);
        $this->assertEquals('2023-05-10 11:43:00',$test->field_datetime);
        $this->assertEquals('2023-05-10',$test->field_date);
        $this->assertEquals('11:43:00',$test->field_time);
        $this->assertEquals('testC',$test->field_enum);
        $this->assertEquals(null,$test->field_object);
        $this->assertEquals('123A',$test->field_calc);
        $this->assertTrue(empty($test->field_oarray));
        $this->assertTrue(empty($test->field_sarray));
        $this->assertTrue(empty($test->field_smap));
    }
    
}