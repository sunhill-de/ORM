<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

class DropTest extends CollectionBase
{
    
    /**
     * @group dropcollection
     */
    public function testDropDummyCollection()
    {
        $test = $this->getDummyStorage();        
        
        $this->assertDatabaseHasTable('dummycollections');
        
        $test->drop();

        $this->assertDatabaseMissingTable('dummycollections');
        
    }
    
    /**
     * @group dropcollection
     */
    public function testDropComplexCollection()
    {
        $test = $this->getComplexStorage();
        
        $this->assertDatabaseHasTable('complexcollections');
        $this->assertDatabaseHasTable('complexcollections_field_oarray');
        $this->assertDatabaseHasTable('complexcollections_field_sarray');
        $this->assertDatabaseHasTable('complexcollections_field_smap');
        
        $test->drop();

        $this->assertDatabaseMissingTable('complexcollections');
        $this->assertDatabaseMissingTable('complexcollections_field_oarray');
        $this->assertDatabaseMissingTable('complexcollections_field_sarray');
        $this->assertDatabaseMissingTable('complexcollections_field_smap');
        
    }
}