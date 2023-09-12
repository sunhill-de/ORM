<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Unit\Storage\Utils\CollectionsAndObjects;
use Illuminate\Support\Facades\DB;

class SeedTest extends DatabaseTestCase
{
    
    use CollectionsAndObjects;
    /**
     * @group storecollection
     * @group collection
     * @group store
     */
    public function testDummyCollection()
    {
        DB::table('dummycollections')->truncate();
        
        $id = DummyCollection::seed([
            'dummy1'=>['dummyint'=>123],
            'dummy2'=>['dummyint'=>234],
            'dummy3'=>['dummyint'=>345]
        ]);
        DummyCollection::seed([
            ['dummyint'=>DummyCollection::getSeedID('dummy2')]
        ]);
        $this->assertDatabaseHas('dummycollections',['id'=>1,'dummyint'=>123]);
        $this->assertDatabaseHas('dummycollections',['id'=>4,'dummyint'=>2]);
        $this->assertEquals(3, $id);
    }
    
    /**
     * @group storecollection
     * @group collection
     * @group store
     */
    public function testStoreComplexCollection()
    {
        DB::table('complexcollections')->truncate();
        $id = ComplexCollection::seed([
            [
                'field_int'=>123,
                'field_char'=>'AAA',
                'field_float'=>1.23,
                'field_datetime'=>'2023-09-07 12:22:00',
                'field_date'=>'2023-09-07',
                'field_time'=>'12:22:00',
                'field_enum'=>'testC',
                'field_text'=>'Lorem ipsum',
                'field_bool'=>true,
                'field_object'=>1,
                'field_collection'=>1,
                'field_oarray'=>[2,3,4],
                'field_sarray'=>['ValA', 'ValB', 'ValC'],
                'field_smap'=>['KeyA'=>'ValA','KeyB'=>'ValB','KeyC'=>'ValC']
            ], 
        ]);
        $this->assertDatabaseHas('complexcollections',['id'=>$id,'field_int'=>123,'field_object'=>1]);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>$id,'index'=>0,'value'=>2]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>$id,'index'=>1,'value'=>'ValB']);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>$id,'index'=>'KeyC','value'=>'ValC']);        
    }
    
    /**
     * @group storecollection
     * @group collection
     * @group store
     */
    public function testStoreComplexCollection_withoutArray()
    {
        DB::table('complexcollections')->truncate();
        $id = ComplexCollection::seed([
            [
                'field_int'=>123,
                'field_float'=>1.23,
                'field_datetime'=>'2023-09-07 12:22:00',
                'field_date'=>'2023-09-07',
                'field_time'=>'12:22:00',
                'field_enum'=>'testC',
                'field_text'=>'Lorem ipsum',
                'field_bool'=>true,
            ],
        ]);
        
        $this->assertDatabaseHas('complexcollections',['id'=>$id,'field_int'=>123,'field_char'=>null,'field_object'=>null]);
        $this->assertDatabaseMissing('complexcollections_field_oarray',['id'=>$id]);
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>$id]);
        $this->assertDatabaseMissing('complexcollections_field_smap',['id'=>$id]);        
    }
}