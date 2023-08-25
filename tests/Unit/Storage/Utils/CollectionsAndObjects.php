<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Utils;

use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

trait CollectionsAndObjects 
{

    protected function getObject($id)
    {
        $test = new Dummy();
        $test->setID($id);
        return $test;
    }
    
    protected function getCollection($id)
    {
        $test = new DummyCollection();
        $test->setID($id);
        return $test;
    }
    
    protected function getComplexCollection($id)
    {
        $test = $this->getMockBuilder(ComplexCollection::class)->disableOriginalConstructor()->getMock();
        $test->expects($this->once())->method('commit')->will($this->returnValue(null));
        $test->expects($this->any())->method('getID')->will($this->returnValue($id));
        $this->setProtectedProperty($test,'id',$id);
        return $test;        
    }
    
    protected function getTag($id)
    {
        $test = new Tag();
        $test->load($id);
        return $test;
    }
    
    protected function getAttribute($id, $name, $value, $type)
    {
        
    }
    
}