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
}