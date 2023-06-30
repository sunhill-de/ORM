<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

class DropTest extends DatabaseTestCase
{
    
    /**
     * @group dropcollection
     */
    public function testDropDummyCollection()
    {
        $collection = new DummyCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseHasTable('dummycollections');
        
        $test->dispatch('drop');

        $this->assertDatabaseMissingTable('dummycollections');
        
    }
    
    /**
     * @group dropcollection
     */
    public function testDropComplexCollection()
    {
        $collection = new ComplexCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseHasTable('complexcollections');
        $this->assertDatabaseHasTable('complexcollections_field_oarray');
        $this->assertDatabaseHasTable('complexcollections_field_sarray');
        $this->assertDatabaseHasTable('complexcollections_field_smap');
        
        $test->dispatch('drop');
        
        $this->assertDatabaseMissingTable('complexcollections');
        $this->assertDatabaseMissingTable('complexcollections_field_oarray');
        $this->assertDatabaseMissingTable('complexcollections_field_sarray');
        $this->assertDatabaseMissingTable('complexcollections_field_smap');
        
    }
}