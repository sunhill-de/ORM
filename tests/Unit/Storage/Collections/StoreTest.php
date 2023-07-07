<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Unit\Storage\Utils\CollectionsAndObjects;

class StoreTest extends DatabaseTestCase
{
    
    use CollectionsAndObjects;
    /**
     * @group storecollection
     */
    public function testDummyCollection()
    {
        $test = new DummyCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $test->dummyint = 707;
        
        $this->assertDatabaseMissing('dummycollections',['dummyint'=>707]);
        
        $storage->dispatch('store');
        
        $id = $test->getID();
        $this->assertDatabaseHas('dummycollections',['id'=>$id,'dummyint'=>707]);
    }
    
    public function testStoreComplexCollection()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $test->field_int = 939;
        $test->field_char = 'ABCD';
        $test->field_float = 9.39;
        $test->field_date = '2023-06-13';
        $test->field_datetime = '2023-06-13 11:11:11';
        $test->field_time = '11:11:11';
        $test->field_enum = 'testC';
        $test->field_text = 'Lorem ipsum';
        $test->field_object = $this->getObject(1);
        $test->field_collection = $this->getCollection(1);
        $test->field_oarray[] = $this->getObject(2);
        $test->field_oarray[] = $this->getObject(3);
        $test->field_oarray[] = $this->getObject(4);
        $test->field_sarray[] = 'ValA';
        $test->field_sarray[] = 'ValB';
        $test->field_sarray[] = 'ValC';
        $test->field_smap['KeyA']='ValA';
        $test->field_smap['KeyB']='ValB';
        $test->field_smap['KeyC']='ValC';
        
        $storage->dispatch('store');

        $id = $test->getID();
        
        $this->assertDatabaseHas('complexcollections',['id'=>$id,'field_int'=>939,'field_object'=>1]);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>$id,'index'=>0,'value'=>2]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>$id,'index'=>1,'value'=>'ValB']);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>$id,'index'=>'KeyC','value'=>'ValC']);        
    }
    
    public function testStoreComplexCollection_withoutArray()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $test->field_int = 939;
        $test->field_char = 'ABCD';
        $test->field_float = 9.39;
        $test->field_date = '2023-06-13';
        $test->field_datetime = '2023-06-13 11:11:11';
        $test->field_time = '11:11:11';
        $test->field_enum = 'testC';
        $test->field_text = 'Lorem ipsum';
        
        $storage->dispatch('store');
        
        $id = $test->getID();
        
        $this->assertDatabaseHas('complexcollections',['id'=>$id,'field_int'=>939,'field_object'=>null]);
        $this->assertDatabaseMissing('complexcollections_field_oarray',['id'=>$id]);
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>$id]);
        $this->assertDatabaseMissing('complexcollections_field_smap',['id'=>$id]);        
    }
}