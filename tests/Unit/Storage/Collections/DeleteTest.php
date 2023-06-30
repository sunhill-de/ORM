<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class DeleteTest extends DatabaseTestCase
{
    
    /**
     * @group deletecollection
     * @group collection
     * @group delete
     */
    public function testDeleteDummyCollection()
    {
        
        $this->assertDatabaseHas('dummycollections',['id'=>2]);
        
        $collection = new DummyCollection();
        $this->setProtectedProperty($collection, 'id', 2);
        
        $storage = new MysqlStorage();
        $storage->setCollection($collection);
        $storage->dispatch('delete');
        
        $this->assertDatabaseMissing('dummycollections',['id'=>2]);        
    }
    
    /**
     * @group deletecollection
     * @group collection
     * @group delete
     */
    public function testDeleteComplexCollection()
    {
        $this->assertDatabaseHas('complexcollections',['id'=>9]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>9,'value'=>'String A']);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>9,'value'=>2]);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>9,'index'=>'KeyA','value'=>'ValueA']);

        $collection = new ComplexCollection();
        $this->setProtectedProperty($collection, 'id', 9);
        
        $storage = new MysqlStorage();
        $storage->setCollection($collection);
        $storage->dispatch('delete');
                
        $this->assertDatabaseMissing('dummycollections',['id'=>9]);        
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>9,'value'=>'String A']);
        $this->assertDatabaseMissing('complexcollections_field_oarray',['id'=>9,'value'=>2]);
        $this->assertDatabaseMissing('complexcollections_field_smap',['id'=>9,'index'=>'KeyA','value'=>'ValueA']);
    }
}