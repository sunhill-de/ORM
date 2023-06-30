<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Illuminate\Support\Facades\Schema;

class MigrateTest extends DatabaseTestCase
{
    
    public function testDummyCollectionFresh()
    {
        Schema::drop('dummycollections');
        
        $collection = new DummyCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseMissingTable('dummycollections');
        
        $test->dispatch('migrate');
        
        $this->assertDatabaseHasTable('dummycollections');        
    }
    
    public function testComplexCollectionFresh()
    {
        Schema::drop('complexcollections');
        Schema::drop('complexcollections_sarray');
        Schema::drop('complexcollections_orray');
        Schema::drop('complexcollections_smap');
        
        $collection = new DummyCollection();
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseMissingTable('complexcollections');
        $this->assertDatabaseMissingTable('complexcollections_sarray');
        $this->assertDatabaseMissingTable('complexcollections_oarray');
        $this->assertDatabaseMissingTable('complexcollections_smap');
        
        $test->dispatch('migrate');
        
        $this->assertDatabaseHasTable('complexcollections');
        $this->assertDatabaseHasTable('complexcollections_sarray');
        $this->assertDatabaseHasTable('complexcollections_oarray');
        $this->assertDatabaseHasTable('complexcollections_smap');
    }
    
}