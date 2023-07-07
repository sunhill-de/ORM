<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Unit\Storage\Utils\CollectionsAndObjects;

class UpdateTest extends DatabaseTestCase
{
   
    use CollectionsAndObjects;
    
    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testDummyCollection()
    {
        $test = new DummyCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $test->setID(1);
        $test->dummyint = 707;
        
        $this->assertDatabaseMissing('dummycollections',['id'=>1,'dummyint'=>707]);
        
        $storage->dispatch('update', 1);
        
        $this->assertDatabaseHas('dummycollections',['id'=>1,'dummyint'=>707]);        
    }
    
    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testComplexCollection_changeAll()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $test->setID(9);
        $test->field_int = 232;
        $test->field_char = 'ZYX';
        $test->field_float = 2.32;
        $test->field_text = 'The ocean is on fire';
        $test->field_datetime = '2023-09-15 16:45:00';
        $test->field_date = '2023-09-15';
        $test->field_time = '16:45:00';
        $test->field_enum = 'testB';        
        $test->field_object = $this->getObject(2);
        $test->field_collection = $this->getCollection(2);
        $test->field_sarray[] = 'ABC';
        $test->field_sarray[] = 'DEF';
        $test->field_sarray[] = 'ROF';
        $test->field_oarray[] = $this->getObject(1);
        $test->field_oarray[] = $this->getObject(2);
        $test->field_oarray[] = $this->getObject(7);
        $test->field_smap['NewKey'] = 'NewValue';
        
        $storage->dispatch('update',9);
        
        $this->assertDatabaseHas('complexcollections',[
            'id'=>9,
            'field_int'=>232,
            'field_char'=>'ZYX',
            'field_float'=>2.32,
            'field_text'=>'The ocean is on fire',
            'field_datetime'=>'2023-09-15 16:45:00',
            'field_date'=>'2023-09-15',
            'field_time'=>'16:45:00',
            'field_enum'=>'testB',
            'field_object'=>2,
            'field_collection'=>2]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>9,'index'=>2,'value'=>'ROF']);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>9,'index'=>2,'value'=>7]);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>9,'index'=>'NewKey','value'=>'NewValue']);
    }
    
    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testComplexCollection_changeSome()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $test->setID(9);
        $test->field_int = 232;
        $test->field_char = 'ZYX';
        
        $storage->dispatch('update',9);        

        $this->assertDatabaseHas('complexcollections',[
            'id'=>9,
            'field_int'=>232,
            'field_char'=>'ZYX',
            'field_float'=>1.11,
            'field_text'=>'Lorem ipsum',
            'field_datetime'=>'1974-09-15 17:45:00',
            'field_date'=>'1974-09-15',
            'field_time'=>'17:45:00',
            'field_enum'=>'testC',
            'field_object'=>1,
            'field_collection'=>1,
            'field_calc'=>'232A'
        ]);        
    }
    
    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testComplexCollection_changeArrayOnly()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $test->setID(9);
        $property = $test->getProperty('field_int');
        $property->loadValue(111);
        $property = $test->getproperty('field_sarray');
        $property->loadValue(['String A','String B']);
        $test->field_sarray[] = 'ROF';
        
        $storage->dispatch('update',9);

        $this->assertDatabaseHas('complexcollections',[
            'id'=>9,
            'field_int'=>111,
            'field_char'=>'ABC',
            'field_float'=>1.11,
            'field_text'=>'Lorem ipsum',
            'field_datetime'=>'1974-09-15 17:45:00',
            'field_date'=>'1974-09-15',
            'field_time'=>'17:45:00',
            'field_enum'=>'testC',
            'field_object'=>1,
            'field_calc'=>'111A',
            'field_collection'=>1]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>9,'index'=>2,'value'=>'ROF']);        
    }
    
    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testComplexCollection_clearArray()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $property = $test->getProperty('field_int');
        $property->loadValue(111);
        $property = $test->getproperty('field_sarray');
        $property->loadValue(['String A','String B']);
        $test->setID(9);
        $test->field_sarray->clear();
        
        $storage->dispatch('update',9);
        
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>9]);
    }

    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testComplexCollection_clearArrayEntry()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $property = $test->getProperty('field_int');
        $property->loadValue(111);
        $property = $test->getproperty('field_sarray');
        $property->loadValue(['String A','String B']);
        $test->setID(9);
        unset($test->field_sarray[1]);
        
        $storage->dispatch('update',9);
        
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>9,'index'=>1]);
        
    }
    
    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testComplexCollection_clearArrayEntryReindex()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $property = $test->getProperty('field_int');
        $property->loadValue(111);
        $property = $test->getproperty('field_sarray');
        $property->loadValue(['String A','String B']);
        $test->setID(9);
        unset($test->field_sarray[0]);
        
        $storage->dispatch('update',9);
        
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>9,'index'=>0,'value'=>'String B']);
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>9,'value'=>'String A']);
        
    }
    
    /**
     * @group updatecollection
     * @group collection
     * @group update
     */
    public function testComplexCollection_changeArrayEntry()
    {
        $test = new ComplexCollection();
        $storage = new MysqlStorage();
        $storage->setCollection($test);
        
        $property = $test->getProperty('field_int');
        $property->loadValue(111);
        $property = $test->getproperty('field_sarray');
        $property->loadValue(['String A','String B']);
        $test->setID(9);
        $test->field_sarray[1] = 'Another';
        
        $storage->dispatch('update',9);
        
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>9,'index'=>1,'value'=>'Another']);
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>9,'value'=>'String B']);
        
    }
    
}