<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Facades\Classes;
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

class PropertiesHaving_PropertyTest extends TestCase
{
 
    /**
     * Tests: PropertiesHaving::getCallingClass()
     */
    public function testGetCallingClass()
    {
        /**
         * Note: this is not a pretty test. It's here for the sake of completeness
         */
        $this->assertEquals(\PHPUnit\Framework\TestResult::class,
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
     * @dataProvider GetCallingClassnameProvider
     * 
     * Tests: getCallingClassname
     */
    public function testGetCallingClassname($class, $expect)
    {
        Classes::flushClasses();
        Classes::registerClass(DummyPropertiesHaving::class);
        Classes::registerClass(AnotherDummyPropertiesHaving::class);        
        $this->assertEquals($expect, $class::callAddProperty()->class);
    }
    
    public function GetCallingClassnameProvider()
    {
        return [
            [DummyPropertiesHaving::class,'DummyPropertiesHaving'],
            [AnotherDummyPropertiesHaving::class,'AnotherDummyPropertiesHaving'],
        ];    
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
    
    /**
     * tests: PropertiesHaving:filterFeature
     */
    public function testFilterFeature()
    {
        $test = ['TestA'=>new FakeProperty('TestA',true), 'TestB'=>new FakeProperty('TestB',false)];
        $result = DummyPropertiesHaving::callStaticMethod('filterFeature', [$test,'somefeature'],false);
        $this->assertEquals(1,count($result));
        $this->assertEquals('TestA',$result['TestA']->name);
    }
    
    public function testGroupResult()
    {
        $test = ['TestA'=>new FakeProperty('TestA',true), 'TestB'=>new FakeProperty('TestB',false)];
        $result = DummyPropertiesHaving::callStaticMethod('groupResult', [$test,'name'],false);
        $this->assertEquals(2,count($result));
        $this->assertEquals('TestA',$result['A']['TestA']->name);
        $this->assertEquals('TestB',$result['B']['TestB']->name);
    }
    
}