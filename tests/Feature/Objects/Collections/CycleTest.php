<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Collections;

use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Facades\Collections;

class CycleTest extends DatabaseTestCase
{
    
    protected function makeDummy(int $dummyint)
    {
        $dummy = new DummyCollection();
        $dummy->dummyint = $dummyint;
        $dummy->commit();
        return $dummy->getID();
    }
    
    /**
     * @group storecollection
     */
    public function testCircle()
    {
        Schema::drop('dummycollections');
        DummyCollection::migrate();

        $this->assertDatabaseHasTable('dummycollections');
        
        $dummy1_id = $this->makeDummy(1234);
        $dummy2_id = $this->makeDummy(2345);
        $dummy3_id = $this->makeDummy(3456);
        $dummy4_id = $this->makeDummy(4555);
        
        $dummy = new DummyCollection();
        $dummy->load($dummy4_id);
        $dummy->dummyint = 4567;
        $dummy->commit();
                
        $reload = new DummyCollection();
        $reload->load($dummy4_id);
        $this->assertEquals(4567,$reload->dummyint);
        
        $search = DummyCollection::search()->where('dummyint','>',3000)->get();
        $this->assertEquals(3456,$search[0]->dummyint);

        DummyCollection::delete($dummy1_id);        
        $this->assertEquals(3,DummyCollection::search()->count());
        
        $complex = new ComplexCollection();
        $complex->field_int = 111;
        $complex->field_char = 'AAA';
        $complex->field_bool = true;
        $complex->field_float = 1.23;
        $complex->field_date = '2023-08-22';
        $complex->field_time = '12:36:00';
        $complex->field_datetime = '2023-08-22 12:36:00';
        $complex->field_collection = $reload;
        $complex->field_enum = 'testA';
        $complex->field_text = 'ABC DEF';
        $complex->commit();
        
        $complex_reload = Collections::loadCollection(complexcollection::class, $complex->getID());
        $this->assertEquals(111, $complex_reload->field_int);
        $this->assertEquals(4567, $complex_reload->field_collection->dummyint);
    }
    
}