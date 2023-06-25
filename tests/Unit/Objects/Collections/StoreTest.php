<?php

namespace Sunhill\ORM\Tests\Unit\Objects\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Unit\Objects\DummyStorage;
use Sunhill\ORM\Facades\Storage;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Tests\Unit\CommonStorage\DummyCollectionStoreStorage;
use Sunhill\ORM\Tests\Utils\TestStorage;
use Sunhill\ORM\Tests\Unit\CommonStorage\ComplexCollectionStoreStorage;
use Sunhill\ORM\Tests\Testobjects\Dummy;

class StoreTest extends DatabaseTestCase
{
    
    /**
     * @group storecollection
     * @group store
     */
    public function testDummyCollectionStore()
    {
        $test = new DummyCollection();
        $storage = new TestStorage();
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $test->dummyint = 333;
        $test->commit();
        
        $compare = new DummyCollectionStoreStorage();
        $compare->assertStorageEquals($storage);        
        $this->assertEquals('store', $storage->last_action);
    }
    
    public function testComplexCollectionStore()
    {
        $test = new ComplexCollection();
        $storage = new TestStorage();
        
        Storage::shouldReceive('createStorage')->once()->andReturn($storage);
        
        $obj1 = new Dummy();
        $obj1->load(2);
        $obj2 = new Dummy();
        $obj2->load(3);
        $obj3 = new Dummy();
        $obj3->load(4);
        
        
        $test->field_int = 333;
        $test->field_char = 'ABC';
        $test->field_float = 1.23;
        $test->field_text = 'Lorem ipsum';
        $test->field_datetime = '2023-05-10 11:43:00';
        $test->field_date = '2023-05-10';
        $test->field_time = '11:43:00';
        $test->field_enum = 'testC';
        $test->field_object = 1;
        $test->field_oarray[] =  $obj1;
        $test->field_oarray[] =  $obj2;
        $test->field_oarray[] =  $obj3;
        $test->field_sarray = ['AAA','BBB','CCC'];
        $test->field_smap = ['KeyA'=>'ValueA','KeyB'=>'ValueB'];
        $test->field_int = 333;
        $test->commit();
        
        $compare = new ComplexCollectionStoreStorage();
        $compare->assertStorageEquals($storage);
    }
    
}