<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\AbstractArrayProperty;

class NonAbstractArrayProperty extends AbstractArrayProperty
{
     
    public function count(): int
    {
        return 2;
    }
    
}

class AbstractArrayPropertyTest extends TestCase
{
     
    public function testSimpleAccess()
    {
        $test = new NonAbstractArrayProperty();
        $this->assertEquals(2, $test->count());
    }
    
}