<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

class StoreTest extends DatabaseTestCase
{
    
    /**
     * @group storecollection
     */
    public function testDummyCollection()
    {
        $test = new DummyCollection();
        $test->dummyint = 707;
        
        $this->assertDatabaseMissing('dummycollections',['dummyint'=>707]);
        
        $test->commit();
        $id = $test->getID();

        $this->assertDatabaseHas('dummycollections',['id'=>$id,'dummyint'=>707]);
    }
    
    public function testStoreComplexCollection()
    {
        $test = new ComplexCollection();

        $test->field_int = 939;
        $test->field_char = 'ABCD';
        $test->field_float = 9.39;
        $test->field_date = '2023-06-13';
        $test->field_datetime = '2023-06-13 11:11:11';
        $test->field_time = '11:11:11';
        $test->field_enum = 'testC';
        $test->field_text = 'Lorem ipsum';
        $test->field_calc = '939A';
        $test->field_object = 1;
        $test->field_oarray = [1,2,3];
        $test->field_sarray = ['ValA','ValB','ValC'];
        $test->field_smap = ['KeyA'=>'ValA','KeyB'=>'ValB','KeyC'=>'ValC'];
        
        $test->commit();
        $id = $test->getID();
        
        $this->assertDatabaseHas('complexcollections',['id'=>$id,'field_int'=>939,'field_object'=>1]);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>$id,'index'=>0,'value'=>1]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>$id,'index'=>1,'value'=>'ValB']);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>$id,'index'=>'KeyC','value'=>'ValC']);        
    }
}