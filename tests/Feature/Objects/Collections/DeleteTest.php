<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

class DeleteTest extends DatabaseTestCase
{
    
    public function testDeleteDummyCollection()
    {
        $test = new DummyCollection();
        
        $this->assertDatabaseHas('dummycollections',['id'=>1]);
        
        $test->delete(1);

        $this->assertDatabaseMissing('dummycollections',['id'=>1]);        
    }
    
    public function testDeleteComplexCollection()
    {
        $test = new ComplexCollection();
        
        $this->assertDatabaseHas('complexcollections',['id'=>9]);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>9]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>9]);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>9]);
        
        $test->delete(9);
        
        $this->assertDatabaseMissing('complexcollections',['id'=>9]);        
        $this->assertDatabaseMissing('complexcollections_field_oarray',['id'=>9]);
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>9]);
        $this->assertDatabaseMissing('complexcollections_field_smap',['id'=>9]);
    }
    
}