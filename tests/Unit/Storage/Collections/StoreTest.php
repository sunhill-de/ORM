<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Collections;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class StoreTest extends CollectionBase
{
    
    /**
     * @group storecollection
     */
    public function testStoreDummyCollection()
    {
        $test = $this->getDummyStorage();
        
        $test->setEntity('dummyint',707);
        
        $this->assertDatabaseMissing('dummycollections',['dummyint'=>707]);
        
        $id = $test->store();

        $this->assertDatabaseHas('dummycollections',['id'=>$id,'dummyint'=>707]);
    }
    
    public function testStoreComplexCollection()
    {
        $test = $this->getComplexStorage();

        $test->setEntity('field_int',939);
        $test->setEntity('field_char','ABCD');
        $test->setEntity('field_float',9.39);
        $test->setEntity('field_date','2023-06-13');
        $test->setEntity('field_datetime','2023-06-13 11:11:11');
        $test->setEntity('field_time','11:11:11');
        $test->setEntity('field_enum','testC');
        $test->setEntity('field_text','Lorem ipsum');
        $test->setEntity('field_calc','939A');
        $test->setEntity('field_object',1);
        $test->setEntity('field_oarray',[1,2,3]);
        $test->setEntity('field_sarray',['ValA','ValB','ValC']);
        $test->setEntity('field_smap',['KeyA'=>'ValA','KeyB'=>'ValB','KeyC'=>'ValC']);
        
        $id = $test->store();
        
        $this->assertDatabaseHas('complexcollections',['id'=>$id,'field_int'=>939,'field_object'=>1]);
        $this->assertDatabaseHas('complexcollections_field_oarray',['id'=>$id,'index'=>0,'value'=>1]);
        $this->assertDatabaseHas('complexcollections_field_sarray',['id'=>$id,'index'=>1,'value'=>'ValB']);
        $this->assertDatabaseHas('complexcollections_field_smap',['id'=>$id,'index'=>'KeyC','value'=>'ValC']);
        
    }
}