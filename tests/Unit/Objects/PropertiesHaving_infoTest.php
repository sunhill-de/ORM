<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Objects\PropertiesHaving;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\PropertiesHavingException;

class PropertiesHaving_infoTest extends TestCase
{
    
    /**
     * Tests: /Objects/PropetiesHaving::setupInfos(), ::addInfo(), ::getInfo(), 
     */
    public function testGetInfo()
    {
        $this->assertEquals('DummyPropertiesHaving',DummyPropertiesHaving::getInfo('name'));
    }
    
    public function testGetInfoWithDefault()
    {
        $this->assertEquals('Default',DummyPropertiesHaving::getInfo('nonexisting','Default'));        
    }
    
    public function testGetInfoWithTranslation()
    {
        $this->assertEquals('Trans:This is a test.',DummyPropertiesHaving::getInfo('test','Default'));
    }
    
    public function testGetInfoInherited()
    {
        $this->assertEquals('AnotherDummyPropertiesHaving',AnotherDummyPropertiesHaving::getInfo('name'));        
    }
    
    /**
     * For some reason (I don't know yet) trans is called but no translation returned. So this test
     * is disables for now. 
     * @todo: Make this test work or at least prove that trans is called (mocking?) 
    public function testTranslation()
    {
        $test = $this->getMockClass('DummyPropertiesHaving');
        
        $test::staticExpects($this->any())->method('translate')->with($this->equalTo('This is a test.'));
        $this->AssertEquals('This is a test.',DummyPropertiesHaving::getInfo('test'));
    } */
    
    public function testAccessError()
    {
        $this->expectException(PropertiesHavingException::class);
        $test = DummyPropertiesHaving::getInfo('something');
    }
    
    /**
     * Tests: /Objects/PropertiesHaving::hasInfo()
     */
    public function testHasInfo()
    {
        $this->assertTrue(DummyPropertiesHaving::hasInfo('name'));
        $this->assertFalse(DummyPropertiesHaving::hasInfo('something'));
    }
    
    /**
     * Tests: /Objects/PropertiesHaving:getAllInfos()
     */
    public function testGetAllInfos()
    {
        $all = AnotherDummyPropertiesHaving::getAllInfos();
        $this->assertArrayHasKey('something', $all);
    }
}