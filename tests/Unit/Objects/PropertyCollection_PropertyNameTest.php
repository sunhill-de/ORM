<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Tests\Unit\Objects\TestCollections\DummyPropertyCollection;
use Sunhill\ORM\Tests\Unit\Objects\TestCollections\AnotherDummyPropertyCollection;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\PropertyList;

class PropertyCollectionNameObject extends ORMObject
{
    
    protected static function setupProperties(PropertyList $list)
    {
        $list->integer('name')->searchable();
        $list->integer('properties');
        $list->integer('infos');
        $list->integer('dirty');
        $list->integer('owner');
        $list->integer('type'); 
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'dummy');
        static::addInfo('table', 'dummies');
        static::addInfo('name_s', 'dummy');
        static::addInfo('name_p', 'dummies');
        static::addInfo('description', 'A dummy test only for testing of property names');
        static::addInfo('options', 0);
    }
    
}

class PropertyCollection_PropertyNameTest extends TestCase
{
     
    public function testSetReservedName()
    {
        $test = new PropertyCollectionNameObject();
        $test->name = 123;
        $test->properties = 123;
        $test->infos = 123;
        $test->dirty = 123;
        $test->owner = 123;
        $test->type = 123;
        
        $property = $test->getProperty('name');
        $this->assertEquals(123, $property->getValue());
        $property = $test->getProperty('properties');
        $this->assertEquals(123, $property->getValue());
        $property = $test->getProperty('infos');
        $this->assertEquals(123, $property->getValue());
        $property = $test->getProperty('dirty');
        $this->assertEquals(123, $property->getValue());
        $property = $test->getProperty('owner');
        $this->assertEquals(123, $property->getValue());
        $property = $test->getProperty('type');
        $this->assertEquals(123, $property->getValue());
    }
    
}