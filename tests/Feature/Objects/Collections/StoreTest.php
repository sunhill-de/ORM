<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Collections;

use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;

class StoreTest extends DatabaseTestCase
{
    
    /**
     * @group storecollection
     */
    public function testLoadDummyCollection()
    {
        $test = new DummyCollection();
        
        $test->dummyint = 398;
        
        $test->commit();
        $id = $test->getID();
        
        $this->assertDatabaseHas('dummycollections',['id'=>$id,'dummyint'=>398]);        
    }
    
    /**
     * @group storecollection
     */
    public function testLoadComplexCollection()
    {
        $test = new ComplexCollection();
        
        $obj1 = Objects::load(1);
        $obj2 = Objects::load(2);
        $obj3 = Objects::load(3);
        
        $test->field_int = 663;
        $test->field_char = 'ABCD';
        $test->field_float = 6.63;
        $test->field_text = "I'll be right there, I'll never leave. All I ask of you is belief.";
        $test->field_datetime = '2023-06-13 12:35:10';
        $test->field_date = '2023-06-13';
        $test->field_time = '12:35:10';
        $test->field_enum = 'TestB';
        $test->field_object = $obj1;
        $test->field_sarray = ['TestA','TestB'];
        $test->field_oarray = [$obj2,$obj3];
        $test->field_smap = ['KeyA'=>'ValueA','KeyB'=>'ValueB'];
        
        $test->commit();
        $id = $test->getID();
        
        $this->assertDatabaseHas('complexcollections',
            [
                'id'=>$id,
                'field_int'=>663,
                'field_char'=>'ABCD',
                'field_float'=>6.63,
                'field_text'=>"I'll be right there, I'll never leave. All I ask of you is belief.",
                'field_datetime'=>'2023-06-13 12:35:10',
                'field_date'=>'2023-06-13',
                'field_time'=>'12:35:10',
                'field_enum'=>'TestB',
                'field_object'=>$obj1,
                ]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>$id,'index'=>0,'value'=>'TestA']);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>$id,'index'=>0,'value'=>2]);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>$id,'index'=>'KeyA','value'=>'ValueA']);
    }
    
    /**
     * @group storecollection
     */
    public function testLoadEmptyComplexCollection()
    {
        $test = new ComplexCollection();
        
        $test->field_int = 663;
        $test->field_char = 'ABCD';
        $test->field_float = 6.63;
        $test->field_text = "I'll be right there, I'll never leave. All I ask of you is belief.";
        $test->field_datetime = '2023-06-13 12:35:10';
        $test->field_date = '2023-06-13';
        $test->field_time = '12:35:10';
        $test->field_enum = 'TestB';
        
        $test->commit();
        $id = $test->getID();
        
        $this->assertDatabaseHas('complexcollections',
            [
                'id'=>$id,
                'field_int'=>663,
                'field_char'=>'ABCD',
                'field_float'=>6.63,
                'field_text'=>"I'll be right there, I'll never leave. All I ask of you is belief.",
                'field_datetime'=>'2023-06-13 12:35:10',
                'field_date'=>'2023-06-13',
                'field_time'=>'12:35:10',
                'field_enum'=>'TestB',
                'field_object'=>null,
            ]);
        $this->assertDatabaseMissing('complexcollections_field_sarray',['id'=>$id]);
        $this->assertDatabaseMissing('complexcollections_field_oarray',['id'=>$id]);
        $this->assertDatabaseMissing('complexcollections_field_smap',['id'=>$id]);
    }
    
}