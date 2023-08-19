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
    public function testAction($collection, $id, $processor, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        
        $test = new $collection();
        
        $storage = new TestStorage();
        
        Storage::shouldReceive('createStorage')->andReturn($storage);
        
        if (!is_null($id)) {
            $this->setProtectedProperty($test, 'id', $id);
        }
        $processor($test);
                
        $this->assertEquals($expect, $storage->last_action);        
    }
    
    public static function ActionProvider()
    {
        return [
           // ************** Loading ************************
            [DummyCollection::class, null, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading(); 
            }, 'collection_load'],
            [ComplexCollection::class, null, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading();
            }, 'collection_load'],
            [Dummy::class, null, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading();
            }, 'object_load'],
            [TestParent::class, null, function($object) 
            { 
                $object->load(1); 
                $object->forceLoading();
            }, 'object_load'],

            // ************** Creating ************************
            [DummyCollection::class, null, function($object) 
            { 
                $object->commit(); 
            }, 'collection_store'],
            [ComplexCollection::class, null, function($object) 
            { 
                $object->commit(); 
            }, 'collection_store'],
            [Dummy::class, null, function($object) 
            { 
                $object->commit(); 
            }, 'object_store'],
            [TestParent::class, null, function($object) 
            { 
                $object->commit(); 
            }, 'object_store'],
            
            // ************** Updating ************************
            [DummyCollection::class, 1, function($object) 
            { 
                $object->dummyint = 1;
                $object->commit(); 
            }, 'collection_update'],
            [ComplexCollection::class, 1, function($object) 
            { 
                $object->field_int = 1;
                $object->commit();
            }, 'collection_update'],
            [Dummy::class, 1, function($object) 
            { 
                $object->dummyint = 1;
                $object->commit();
            }, 'object_update'],
            [TestParent::class, 1, function($object) 
            { 
                $object->parentint = 1;
                $object->commit();
            }, 'object_update'],
            
            // ****************** Deleting ********************
            [DummyCollection::class, null, function($object)
            {
                $object->delete(1);
            }, 'collection_delete'],
            [ComplexCollection::class, null, function($object)
            {
                $object->delete(9);
            }, 'collection_delete'],
            [Dummy::class, null, function($object)
            {
                $object->delete(1);
            }, 'object_delete'],
            [TestParent::class, null, function($object)
            {
                $object->delete(1);
            }, 'object_delete'],
            
         ];
    }
    
    /**
     * @dataProvider SearchActionProvider
     */
    public function testSearchAction($collection, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        
        $storage = new TestStorage();
        
        Storage::shouldReceive('createStorage')->andReturn($storage);
        
        $query = $collection::search();
        
        $this->assertEquals($expect, $storage->last_action);
    }
    
    public static function SearchActionProvider()
    {
        return [
            [DummyCollection::class,'collection_search'],
            [ComplexCollection::class,'collection_search'],
            [Dummy::class,'object_search'],
            [TestParent::class,'object_search'],
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