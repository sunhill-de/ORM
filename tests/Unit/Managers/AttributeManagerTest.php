<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Attributes;

class AttributeManagerTest extends DatabaseTestCase
{

    public function testGetAvaiableAttributesForClass()
    {
        $this->assertEquals(5, count(Attributes::getAvaiableAttributesForClass(Dummy::class)));
        $this->assertEquals('text_attribute', Attributes::getAvaiableAttributesForClass(Dummy::class)[4]->name);
    }
    
    public function testGetAvaiableAttributesForClassWithFilter()
    {
        $this->assertEquals(4, count(Attributes::getAvaiableAttributesForClass(Dummy::class,['general_attribute'])));
    }
    
}
