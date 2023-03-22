<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Objects\PropertiesHaving;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\PropertiesHavingException;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Properties\PropertyTimestamp;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyArrayOfStrings;
use Sunhill\ORM\Properties\PropertyArrayOfObjects;
use Sunhill\ORM\Properties\PropertyCalculated;

class PropertiesHaving_PropertyTests extends TestCase
{
 
    /**
     * Tests: PropertiesHaving::getCallingClass()
     */
    public function testGetCallingClass()
    {
        /**
         * Note: this is not a pretty test. It's here for the sake of completeness
         */
        $this->assertEquals(\PHPUnit\Framework\TestCase::class,
            DummyPropertiesHaving::callStaticMethod('getCallingClass'));    
    }
    
    /**
     * Tests: PropertiesHaving::getPropertClass()
     */
    public function testGetPropertyClass()
    {
        $this->assertEquals('\\'.PropertyInteger::class,
            DummyPropertiesHaving::callStaticMethod('getPropertyClass',['Integer'], false));
    }
    
    /**
     * Tests: PropertiesHaving::getPropertClass()
     */
    public function testGetPropertyClass_failure()
    {
        $this->expectException(ORMException::class);
        DummyPropertiesHaving::callStaticMethod('getPropertyClass',['NoneExisting'],false);
    }
    
    /**
     * Tests: PropertiesHaving::createProperty
     */
    public function testCreateProperty()
    {
        $property = DummyPropertiesHaving::callStaticMethod('createProperty',['test','Integer','placeholder'],false);
        $this->assertEquals(PropertyInteger::class, $property::class);
    }
    
    /**
     * @dataProvider PropertyMethodProvider
     * @param unknown $method
     * @param unknown $property_class
     * tests: See list below
     */
    public function testPropertyMethods($method, $property_class)
    {
        $property = DummyPropertiesHaving::callMethod($method);
        $this->assertEquals($property_class,$property::class);
    }
    
    public function PropertyMethodProvider()
    {
        return [
            ['integer',PropertyInteger::class],
            ['timestamp', PropertyTimestamp::class],
            ['varchar', PropertyVarchar::class],
            ['object', PropertyObject::class],
            ['text', PropertyText::class],
            ['enum', PropertyEnum::class],
            ['datetime', PropertyDatetime::class],
            ['time', PropertyTime::class],
            ['date', PropertyDate::class],
            ['float', PropertyFloat::class],
            ['arrayofstrings', PropertyArrayOfStrings::class],
            ['arrayofobjects', PropertyArrayOfObjects::class],
            ['calculated', PropertyCalculated::class]
        ];
    }
    
    /**
     * Tests: PropertiesHaving::getPropertyObject
     */
    public function testGetPropertyObject()
    {
        $property = AnotherDummyPropertiesHaving::getPropertyObject('test');
        $this->assertEquals(PropertyInteger::class, $property::class);
        $this->assertNull(AnotherDummyPropertiesHaving::getPropertyObject('nonexisting'));
    }
    
    /**
     * tests: PropertiesHaving::prepareGroup
     */
    public function testPrepareGroup()
    {
        $this->assertEquals('getTest',DummyPropertiesHaving::callStaticMethod('prepareGroup',['test']));
        $this->assertNull(DummyPropertiesHaving::callStaticMethod('prepareGroup',[null]));
    }
    
    /**
     * tests: PropertiesHaving::getAllProperties
     */
    public function testGetAllProperties()
    {
        $this->assertTrue(empty(DummyPropertiesHaving::callStaticMethod('getAllProperties')));
        $this->assertFalse(empty(AnotherDummyPropertiesHaving::callStaticMethod('getAllProperties')));
    }
}