<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Tests\Unit\Objects\TestCollections\DummyPropertyCollection;
use Sunhill\ORM\Tests\Unit\Objects\TestCollections\AnotherDummyPropertyCollection;
use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Objects\PropertyList;

class NameCollection extends Collection
{
    protected static function setupProperties(PropertyList $list)
    {
        $list->varchar('name')->setMaxLen(20)->searchable()->setDefault(null);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'namecollection');
        static::addInfo('table', 'namecollections');
        static::addInfo('description', 'A test collection for special property names.');
        static::addInfo('options', 0);
    }
    
}

class PropertyCollection_PropertyTest extends TestCase
{
     
    public function testDummyCollectionStatic()
    {
        $this->assertTrue(DummyPropertyCollection::definesProperty('testint'));
        $this->assertFalse(DummyPropertyCollection::definesProperty('notexisting'));
        $this->assertEquals(['testint','teststring'],array_keys(DummyPropertyCollection::getPropertyDefinition()));
        $this->assertEquals(['testint','teststring'],array_keys(DummyPropertyCollection::getAllPropertyDefinitions()));
    }
    
    public function testDummyCollectionNonStatic()
    {
        $test = new DummyPropertyCollection();
        $this->assertTrue($test->hasProperty('testint'));
        $this->assertFalse($test->hasProperty('notexisting'));
        $this->assertEquals(['testint','teststring'],array_keys($test->getProperties()));
        $this->assertEquals(['testint','teststring'],array_keys($test->getAllProperties()));
    }
    
    public function testAnotherDummyStatic()
    {
        $this->assertTrue( AnotherDummyPropertyCollection::definesProperty('anothertestint') );
        $this->assertFalse( AnotherDummyPropertyCollection::definesProperty('testint') );
        $this->assertFalse( AnotherDummyPropertyCollection::definesProperty('nonexisting') );
        $this->assertEquals(['anothertestint','anotherteststring'],array_keys(AnotherDummyPropertyCollection::getPropertyDefinition()));
        $this->assertEquals(['anothertestint','anotherteststring','testint','teststring'],array_keys(AnotherDummyPropertyCollection::getAllPropertyDefinitions()));
    }
    
    public function testAnotherDummyNonStatic()
    {
        $test = new AnotherDummyPropertyCollection();
        $this->assertTrue( $test->hasProperty('anothertestint') );
        $this->assertTrue( $test->hasProperty('testint') );
        $this->assertFalse( $test->hasProperty('nonexisting') );
        $this->assertEquals(['anothertestint','anotherteststring'],array_keys($test->getProperties()));
        $this->assertEquals(['anothertestint','anotherteststring','testint','teststring'],array_keys($test->getAllProperties()));
    }
    
    public function testDummySetValue()
    {
        $test = new DummyPropertyCollection();
        $this->assertFalse($test->isDirty());
        $test->testint = 10;
        $this->assertTrue($test->isDirty());
        $this->assertEquals(10, $test->testint);
        
        $property = $test->getProperty('testint');
        $this->assertEquals(10, $property->getValue());
    }
    
    public function testDummyUnintialized()
    {
        $this->expectException(PropertyException::class);
        $test = new DummyPropertyCollection();
        $a = $test->testint;
    }
    
    public function testDummyUnknown()
    {
        $this->expectException(PropertyException::class);
        $test = new DummyPropertyCollection();
        $a = $test->nonexisting;
    }
    
    public function testDummyUnknownWrite()
    {
        $this->expectException(PropertyException::class);
        $test = new DummyPropertyCollection();
        $test->nonexisting = 10;        
    }
    
    public function testTestSimpleChild()
    {
        $this->assertEquals([], TestSimpleChild::getPropertyDefinition());        
    }
    
    public function testNameCollection()
    {
         $test = new NameCollection();
         $test->name = 'ABC';
         $this->assertEquals('ABC',$test->name);
         $this->assertEquals('name', $test->getProperty('name')->getName());
    }
}