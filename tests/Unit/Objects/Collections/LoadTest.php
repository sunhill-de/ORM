<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Unit\Objects\DummyStorage;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Properties\PropertyMap;

class LoadTest extends DatabaseTestCase
{
    
    /**
     * @group loadcollection
     */
    public function testDummyCollectionPreloading()
    {
        $test = new DummyCollection();
        $storage = new DummyStorage(DummyCollection::class);
        
        $this->callProtectedMethod($test, 'prepareStorage',[$storage]);
        $list = $storage->getEntitiesOfStorageID('dummycollections');
        
        $this->assertEquals('dummyint', $list['dummyint']->name);
        $this->assertEquals(PropertyInteger::class, $list['dummyint']->type);
    }
    
    /**
     * @group loadcollection
     */
    public function testComplexCollectionPreloading()
    {
        $test = new ComplexCollection();
        $storage = new DummyStorage(ComplexCollection::class);

        $this->callProtectedMethod($test, 'prepareStorage',[$storage]);
        $list = $storage->getEntitiesOfStorageID('complexcollections');
        
        $this->assertEquals('field_int', $list['field_int']->name);
        $this->assertEquals(PropertyMap::class, $list['field_smap']->type);
        $this->assertEquals(PropertyArray::class, $list['field_sarray']->type);
    }
    
    /**
     * @group loadcollection
     */
    public function testDummyCollectionLoading()
    {
        $test = new DummyCollection();
        $storage = new DummyStorage(DummyCollection::class);
        
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
        $storage = new DummyStorage(ComplexCollection::class);
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
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
        $storage = new DummyStorage(ComplexCollection::class.'empty');
        
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