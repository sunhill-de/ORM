<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;

class UpdateTest extends DatabaseTestCase
{
   
    public function testDummyCollection()
    {
        $test = new DummyCollection();
        $test->load(1);
        $test->dummyint = 707;
        
        $this->assertDatabaseHas('dummycollections',['id'=>1,'dummyint'=>707]);
        
        $test->commit();
        
        $this->assertDatabaseHas('dummycollections',['id'=>1,'dummyint'=>707]);
        
    }
    
}