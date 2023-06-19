<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\PropertiesCollectionException;

class PropertyCollection_infoTest extends TestCase
{
    
    /**
     * Tests: /Objects/PropetiesHaving::setupInfos(), ::addInfo(), ::getInfo(), 
     */
    public function testGetInfo()
    {
        $this->assertEquals('DummyPropertyCollection',DummyPropertyCollection::getInfo('name'));
    }
    
    public function testGetInfoWithDefault()
    {
        $this->assertEquals('Default',DummyPropertyCollection::getInfo('nonexisting','Default'));        
    }
    
    public function testGetInfoWithTranslation()
    {
        $this->assertEquals('Trans:This is a test.',DummyPropertyCollection::getInfo('test','Default'));
    }
    
    public function testGetInfoInherited()
    {
        $this->assertEquals('AnotherDummyPropertyCollection',AnotherDummyPropertyCollection::getInfo('name'));        
    }
    
    /**
     * For some reason (I don't know yet) trans is called but no translation returned. So this test
     * is disables for now. 
     * @todo: Make this test work or at least prove that trans is called (mocking?) 
    public function testTranslation()
    {
        $test = $this->getMockClass('DummyPropertyCollection');
        
        $test::staticExpects($this->any())->method('translate')->with($this->equalTo('This is a test.'));
        $this->AssertEquals('This is a test.',DummyPropertyCollection::getInfo('test'));
    } */
    
    public function testAccessError()
    {
        $this->expectException(PropertiesCollectionException::class);
        $test = DummyPropertyCollection::getInfo('something');
    }
    
    /**
     * Tests: /Objects/PropertyCollection::hasInfo()
     */
    public function testHasInfo()
    {
        $this->assertTrue(DummyPropertyCollection::hasInfo('name'));
        $this->assertFalse(DummyPropertyCollection::hasInfo('something'));
    }
    
    /**
     * Tests: /Objects/PropertyCollection:getAllInfos()
     */
    public function testGetAllInfos()
    {
        $all = AnotherDummyPropertyCollection::getAllInfos();
        $this->assertArrayHasKey('something', $all);
    }
}