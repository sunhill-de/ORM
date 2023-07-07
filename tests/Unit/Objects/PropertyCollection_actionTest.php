<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\PropertiesCollectionException;
use Sunhill\ORM\Tests\Utils\TestStorage;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\ObjectData;

class PropertyCollection_actionTest extends TestCase
{

    /**
     * @dataProvider ActionProvider
     */
    public function testAction($collection, $processor, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        
        $test = new $collection();
        
        $storage = new TestStorage();
        
        Storage::shouldReceive('createStorage')->andReturn($storage);
        
        $processor($test);
        
        $this->assertEquals($expect, $storage->last_action);        
    }
    
    public function ActionProvider()
    {
        return [
            [DummyCollection::class, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading(); 
            }, 'collection_load'],
            [ComplexCollection::class, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading();
            }, 'collection_load'],
            [Dummy::class, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading();
            }, 'object_load'],
            [TestParent::class, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading();
            }, 'object_load'],

            [DummyCollection::class, function($object) 
            { 
                $object->commit(); 
            }, 'collection_store'],
            [ComplexCollection::class, function($object) 
            { 
                $object->commit(); 
            }, 'collection_store'],
            [Dummy::class, function($object) 
            { 
                $object->commit(); 
            }, 'object_store'],
            [TestParent::class, function($object) 
            { 
                $object->commit(); 
            }, 'object_store'],
            
            [DummyCollection::class, function($object) 
            { 
                $this->setProtectedProperty($object, 'id', 1);
                $object->dummyint = 1;
                $object->commit(); 
            }, 'collection_update'],
            [ComplexCollection::class, function($object) 
            { 
                $this->setProtectedProperty($object, 'id', 1);
                $object->field_int = 1;
                $object->commit();
            }, 'collection_update'],
            [Dummy::class, function($object) 
            { 
                $this->setProtectedProperty($object, 'id', 1);
                $object->dummyint = 1;
                $object->commit();
            }, 'object_update'],
            [TestParent::class, function($object) 
            { 
                $this->setProtectedProperty($object, 'id', 1);
                $object->parentint = 1;
                $object->commit();
            }, 'object_update'],
            
         ];
    }
    
    public function testObjectFields_insert()
    {
        Classes::registerClass(Dummy::class);
        $object = new Dummy();
        
        $storage = new TestStorage();
        Storage::shouldReceive('createStorage')->andReturn($storage);
        ObjectData::shouldReceive('getDBTime')->andReturn('2023-07-07 10:05:00');
        ObjectData::shouldReceive('getUUID')->andReturn('abcd-deef');
        
        $object->dummyint = 123;
        $object->commit();
        
        $this->assertEquals('2023-07-07 10:05:00', $object->_created_at);
        $this->assertEquals('2023-07-07 10:05:00', $object->_updated_at);
        $this->assertEquals('abcd-deef', $object->_uuid);
    }
    
    public function testObjectFields_update()
    {
        Classes::registerClass(Dummy::class);
        $object = new Dummy();
        
        $storage = new TestStorage();
        Storage::shouldReceive('createStorage')->andReturn($storage);
        ObjectData::shouldReceive('getDBTime')->andReturn('2023-07-07 10:05:00');
        ObjectData::shouldReceive('getUUID')->andReturn('abcd-deef');
        
        $this->setProtectedProperty($object, 'id', 1);
        $object->dummyint = 123;
        $object->commit();
        
        $this->assertEquals('2023-07-07 10:05:00', $object->_updated_at);
    }
    
}