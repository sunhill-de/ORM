<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Objects\Tag;

class TagTest extends DatabaseTestCase
{

    public function testLoadTag()
    {
        $test = new Tag();
        $test->load(1);
        $this->assertEquals('TagA',$test->getName());
        $this->assertEquals('TagA',$test->getFullPath());
    }
    
    public function testLazyLoad()
    {
        $test = new Tag();
        $test->load(1);
        $this->assertEquals('',$this->getProtectedProperty($test, 'name'));
        $this->assertEquals('TagA',$test->getName());
        $this->assertEquals('TagA',$this->getProtectedProperty($test, 'name'));        
    }
    
    public function testGetFullpath()
    {
        $test = new Tag();
        $test->load(8);
        $this->assertEquals('TagF.TagG.TagE',$test->getFullPath());
    }
    
    public function testGetterAndSetter()
    {
        $test = new Tag();
        $parent = new Tag();
        
        $test->setParent($parent)->setName('Test')->setOptions(TO_LEAFABLE);
        $this->assertEquals($parent,$test->getParent());
        $this->assertEquals('Test',$test->getName());
        $this->assertEquals(TO_LEAFABLE,$test->getOptions());
        $this->assertTrue($test->isLeafable());
        $test->unsetLeafable();
        $this->assertFalse($test->isLeafable());
        $test->setLeafable();
        $this->assertTrue($test->isLeafable());
        
    }
}