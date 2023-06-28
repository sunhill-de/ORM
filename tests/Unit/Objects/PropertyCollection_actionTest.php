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
}