<?php

namespace Sunhill\ORM\Tests\Feature\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Managers\TagManager;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Objects\Tag;
use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\Descriptor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Objects\TagException;
use Sunhill\ORM\Facades\Collections;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Illuminate\Support\Facades\Schema;

class CollectionManagerTest extends DatabaseTestCase
{

    public function testLoadCollection()
    {
        Collections::registerCollection(DummyCollection::class);
        
        $collection = Collections::loadCollection('dummycollection',1);
        
        $this->assertEquals(123, $collection->dummyint);
    }
    
    public function testLoadCollectionWithoutRegistering()
    {
        $collection = Collections::loadCollection(DummyCollection::class,1);
        
        $this->assertEquals(123, $collection->dummyint);
    }
 
    public function testGetRegisteredCollections()
    {
        Collections::registerCollection(DummyCollection::class);
        Collections::registerCollection(ComplexCollection::class);
        
        $collections = array_keys(Collections::getRegisteredCollections());
        
        $this->assertEquals(['dummycollection','complexcollection'],$collections);
    }
    
    public function testMigrateCollections()
    {
        Schema::drop('dummycollections');
        Schema::drop('complexcollections');
        Schema::drop('complexcollections_field_sarray');
        Schema::drop('complexcollections_field_oarray');
        Schema::drop('complexcollections_field_smap');

        Collections::registerCollection(DummyCollection::class);
        Collections::registerCollection(ComplexCollection::class);
        
        Collections::migrateCollections();
        
        $this->assertDatabaseHasTable('complexcollections');
        $this->assertDatabaseHasTable('complexcollections_field_sarray');
        $this->assertDatabaseHasTable('complexcollections_field_oarray');
        $this->assertDatabaseHasTable('complexcollections_field_smap');
        $this->assertDatabaseHasTable('dummycollections');
        
    }
    
}
