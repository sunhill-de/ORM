<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Utils\TestStorage;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;

class StorageBaseTest extends TestCase
{
        
    public function testEntitiesBasic()
    {
        $test = new TestStorage();
        $this->assertFalse($test->hasEntity('test'));
        $this->assertEquals([], $test->getStorageIDs());
        
        $test->createEntity('test','test_id');
        
        $this->assertTrue($test->hasEntity('test'));
        $this->assertEquals(['test_id'], $test->getStorageIDs());
    }
        
    public function testEntitiesElement()
    {
        $test = new TestStorage();
        
        $test->createEntity('test','test_id')->setType(PropertyInteger::class)->setValue(2)->setShadow(1);
        
        $this->assertEquals('test', $test->getEntity('test')->getName());
        $this->assertEquals('test_id', $test->getEntity('test')->getStorageID());
        $this->assertEquals(PropertyInteger::class, $test->getEntity('test')->getType());
        $this->assertEquals(2, $test->getEntity('test')->getValue());
        $this->assertEquals(1, $test->getEntity('test')->getShadow());
    }

    public function testEntitiesOfStorageID()
    {
        $test = new TestStorage();
        
        $test->createEntity('test','test_id')->setType(PropertyInteger::class)->setValue(2)->setShadow(1);
        $test->createEntity('test2','test2_id')->setType(PropertyInteger::class)->setValue(2)->setShadow(1);

        $list = $test->getEntitiesOfStorageID('test_id');
        
        $this->assertEquals(1,count($list));
        $this->assertEquals('test',$list['test']->getName());
    }
    
    public function testGetEntitiesOfType()
    {
        $test = new TestStorage();
        
        $test->createEntity('test','test_id')->setType(PropertyInteger::class)->setValue(2)->setShadow(1);
        $test->createEntity('test2','test_id')->setType(PropertyVarchar::class)->setValue(2)->setShadow(1);
        
        $list = $test->getEntitiesOfType(PropertyInteger::class);
        
        $this->assertEquals(1,count($list));
        $this->assertEquals('test',$list['test']->getName());        
    }
    
    public function testGetEntityValue()
    {
        $test = new TestStorage();
        
        $test->createEntity('test','test_id')->setType(PropertyInteger::class);
        $test->test = 2;
        
        $this->assertEquals(2, $test->test);
    }
    
    public function testSourceType()
    {
        $test = new TestStorage();
        $test->setSourceType('collection');
        
        $this->assertEquals('collection', $test->getSourceType());
    }
    
    public function testStorageEquals()
    {
        $test1 = new TestStorage();
        $test1->createEntity('test','test_id')->setType(PropertyInteger::class)->setValue(2)->setShadow(1);
        
        $test2 = new TestStorage();
        $test2->createEntity('test','test_id')->setType(PropertyInteger::class)->setValue(2)->setShadow(1);
        
        $test1->assertStorageEquals($test2, true);
    }

    public function testStorageEqualsFail()
    {
        $this->expectException('PHPUnit\Framework\ExpectationFailedException');
        $test1 = new TestStorage();
        $test1->createEntity('test','test_id')->setType(PropertyInteger::class)->setValue(2)->setShadow(1);
        
        $test2 = new TestStorage();
        $test2->createEntity('test','test_id')->setType(PropertyVarchar::class)->setValue(2)->setShadow(1);
        
        $test1->assertStorageEquals($test2, true);
    }
    
}