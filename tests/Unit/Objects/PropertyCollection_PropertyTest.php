<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\PropertyCollection;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\PropertyCollectionException;
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

class FakeProperty 
{
    
    public $name;
    
    protected $feature;
    
    public function __construct(string $name, bool $feature)
    {
        $this->name = $name;
        $this->feature = $feature;
    }
    
    public function hasFeature(string $test): bool
    {
        return $this->feature;
    }
    
    public function getName(): string
    {
        return substr($this->name,-1);
    }
    
}

class PropertyCollection_PropertyTest extends TestCase
{
 
    /**
     * Tests: PropertyCollection::getCallingClass()
     */
    public function testGetCallingClass()
    {
        /**
         * Note: this is not a pretty test. It's here for the sake of completeness
         */
        $this->assertEquals(\PHPUnit\Framework\TestCase::class,
            DummyPropertyCollection::callStaticMethod('getCallingClass'));    
    }
    
    /**
     * Tests: PropertyCollection::getPropertClass()
     */
    public function testGetPropertyClass()
    {
        $this->assertEquals('\\'.PropertyInteger::class,
            DummyPropertyCollection::callStaticMethod('getPropertyClass',['Integer'], false));
    }
   
    /**
     * @dataProvider GetCallingClassnameProvider
     * 
     * Tests: getCallingClassname
     */
    public function testGetCallingClassname($class, $expect)
    {
        Classes::flushClasses();
        Classes::registerClass(DummyPropertyCollection::class);
        Classes::registerClass(AnotherDummyPropertyCollection::class);        
        $this->assertEquals($expect, $class::callAddProperty()->class);
    }
    
    public function GetCallingClassnameProvider()
    {
        return [
            [DummyPropertyCollection::class,'DummyPropertyCollection'],
            [AnotherDummyPropertyCollection::class,'AnotherDummyPropertyCollection'],
        ];    
    }
    
    /**
     * Tests: PropertyCollection::getPropertClass()
     */
    public function testGetPropertyClass_failure()
    {
        $this->expectException(ORMException::class);
        DummyPropertyCollection::callStaticMethod('getPropertyClass',['NoneExisting'],false);
    }
    
    /**
     * Tests: PropertyCollection::createProperty
     */
    public function testCreateProperty()
    {
        $property = DummyPropertyCollection::callStaticMethod('createProperty',['test','Integer','placeholder'],false);
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
        $property = DummyPropertyCollection::callMethod($method);
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
     * Tests: PropertyCollection::getPropertyObject
     */
    public function testGetPropertyObject()
    {
        $property = AnotherDummyPropertyCollection::getPropertyObject('test');
        $this->assertEquals(PropertyInteger::class, $property::class);
        $this->assertNull(AnotherDummyPropertyCollection::getPropertyObject('nonexisting'));
    }
    
    /**
     * tests: PropertyCollection::prepareGroup
     */
    public function testPrepareGroup()
    {
        $this->assertEquals('getTest',DummyPropertyCollection::callStaticMethod('prepareGroup',['test']));
        $this->assertNull(DummyPropertyCollection::callStaticMethod('prepareGroup',[null]));
    }
    
    /**
     * tests: PropertyCollection::getAllProperties
     */
    public function testGetAllProperties()
    {
        $this->assertTrue(empty(DummyPropertyCollection::callStaticMethod('getAllProperties')));
        $this->assertFalse(empty(AnotherDummyPropertyCollection::callStaticMethod('getAllProperties')));
    }
    
    /**
     * tests: PropertyCollection:filterFeature
     */
    public function testFilterFeature()
    {
        $test = ['TestA'=>new FakeProperty('TestA',true), 'TestB'=>new FakeProperty('TestB',false)];
        $result = DummyPropertyCollection::callStaticMethod('filterFeature', [$test,'somefeature'],false);
        $this->assertEquals(1,count($result));
        $this->assertEquals('TestA',$result['TestA']->name);
    }
    
    public function testGroupResult()
    {
        $test = ['TestA'=>new FakeProperty('TestA',true), 'TestB'=>new FakeProperty('TestB',false)];
        $result = DummyPropertyCollection::callStaticMethod('groupResult', [$test,'name'],false);
        $this->assertEquals(2,count($result));
        $this->assertEquals('TestA',$result['A']['TestA']->name);
        $this->assertEquals('TestB',$result['B']['TestB']->name);
    }
    
}