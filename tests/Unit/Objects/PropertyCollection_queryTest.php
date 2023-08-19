<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\PropertiesCollectionException;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyInformation;

class PropertyCollection_queryTest extends TestCase
{

    /**
     * @dataProvider DynamicPropertiesProvider
     */
    public function testDynamicProperties($class, $query_mod, $expect)
    {
        $object = new $class();
        $query = $object->propertyQuery();
        $result = $query_mod($query);
        if (is_callable($expect)) {
            $this->assertTrue($expect($result));
        } else {
            $this->assertEquals($expect, $result);
        }
    }
    
    public static function DynamicPropertiesProvider()
    {
        return [
            [DummyCollection::class, function($query) { return $query->count(); }, 1],
            [ComplexCollection::class, function($query) { return $query->count(); }, 15],
            [Dummy::class, function ($query) { return $query->count(); }, 10],
            [TestParent::class, function($query) { return $query->count(); }, 28],
            [TestChild::class, function($query) { return $query->count(); }, 45],
            [Dummy::class, function ($query) { return $query->where('owner', Dummy::class)->count(); }, 1],
            [TestParent::class, function($query) { return $query->where('type', PropertyDate::class)->count(); }, 1],
            [TestParent::class, function($query)
            {
                return $query->where('name', 'parentchar')->first();
            }, function($result)
            {
                return $result->max_len == 255;
            }],            
            [TestParent::class, function($query) 
            {
                return $query->where('name', 'parentcollection')->first(); 
            }, function($result)
            {
                return $result->allowed_collection == DummyCollection::class;
            }],
            [TestParent::class, function($query)
            {
                return $query->where('name', 'parentoarray')->first();
            }, function($result)
            {
                return $result->element_type == PropertyObject::class;
            }],
            [TestParent::class, function($query)
            {
                return $query->where('name', 'parentoarray')->first();
            }, function($result)
            {
                return $result->allowed_classes == ['dummy'];
            }],
            [TestParent::class, function($query)
            {
                return $query->where('name', 'parentenum')->first();
            }, function($result)
            {
                return $result->enum_values == ['testA','testB','testC'];
            }],
            [TestParent::class, function($query)
            {
                return $query->where('name', 'parentobject')->first();
            }, function($result)
            {
                return $result->allowed_classes == ['dummy'];
            }],
            [TestParent::class, function($query) { return $query->where('type', PropertyDate::class)->first(); }, function($result) { return $result->name == 'parentdate'; }],
            [TestSimpleChild::class, function($query) { return $query->count(); }, 28],
            ];    
    }

    public function testInformation()
    {
        $object = new TestParent();
        $object->getProperty('parentinformation')->setPath('some.path');
        $query = $object->propertyQuery()->where('name','parentinformation')->first();
        $this->assertEquals('some.path', $query->path);
    }
    
    public function testKeyfield()
    {
        $object = new TestParent();
        $query = $object->propertyQuery()->where('name','parentkeyfield')->first();
        $this->assertEquals(':parentchar (:parentint)', $query->build_rule);
    }
    
    public function testValue()
    {
        $test = new Dummy();
        $test->dummyint = 123;
        $result = $test->propertyQuery()->where('name','dummyint')->first();
        $this->assertEquals(123, $result->value);
    }
    
    /**
     * @dataProvider StaticPropertiesProvider
     */
    public function testStaticProperties($class, $query_mod, $expect)
    {
        $query = $class::staticPropertyQuery();
        $result = $query_mod($query);
        if (is_callable($expect)) {
            $this->assertTrue($expect($result));
        } else {
            $this->assertEquals($expect, $result);
        }        
    }
    
    public static function StaticPropertiesProvider()
    {
        return [
            [DummyCollection::class, function($query) { return $query->count(); }, 1],
            [ComplexCollection::class, function($query) { return $query->count(); }, 15],
            [Dummy::class, function ($query) { return $query->count(); }, 10],
            [TestParent::class, function($query) { return $query->count(); }, 28],
            [TestChild::class, function($query) { return $query->count(); }, 45],
            [Dummy::class, function ($query) { return $query->where('owner', Dummy::class)->count(); }, 1],
            [TestParent::class, function($query) { return $query->where('type', PropertyDate::class)->count(); }, 1],
            [TestParent::class, function($query) { return $query->where('type', PropertyDate::class)->first(); }, function($result) { return $result->name == 'parentdate'; }],
            [TestSimpleChild::class, function($query) { return $query->count(); }, 28]            
        ];
    }
}