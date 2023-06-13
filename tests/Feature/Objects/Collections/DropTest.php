<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

class DropTest extends DatabaseTestCase
{
    
    public function testDropDummyCollection()
    {
        $test = new DummyCollection();
        
        $this->assertDatabaseHasTable('dummycollections');
        
        $test::drop();

        $this->assertDatabaseMissingTable('dummycollections');        
    }
    
    public function testDropComplexCollection()
    {        
        $this->assertDatabaseHasTable('complexcollections');
        $this->assertDatabaseHasTable('complexcollections_field_oarray');
        $this->assertDatabaseHasTable('complexcollections_field_sarray');
        $this->assertDatabaseHasTable('complexcollections_field_smap');
        
        ComplexCollection::drop();
        
        $this->assertDatabaseMissingTable('complexcollections');        
        $this->assertDatabaseMissingTable('complexcollections_field_oarray');
        $this->assertDatabaseMissingTable('complexcollections_field_sarray');
        $this->assertDatabaseMissingTable('complexcollections_field_smap');
    }
    
}