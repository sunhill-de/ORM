<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class ObjectReadTest extends \Tests\sunhill_testcase_nodb
{

    public function testStorageCreation() {
        $object = new \Sunhill\Test\ts_objectunit();
        $object->storage_values = [
            'id'=>1,
            'created_at'=>'2019-10-06 12:05:00',
            'modified_at'=>'2019-10-06 12:05:00',
            'intvalue'=>123,
            'objectvalue'=>2,
            'sarray'=>['ABC','DEF','GHI'],
            'oarray'=>[3,4,5],
            'calcvalue'=>'123A',
            'tags'=>[1,2,3,4],
        ];
        $object->public_load(1);
        $this->assertEquals(1,$object->get_id());
        return $object;
    }
    
    /**
     * @depends testStorageCreation
     */
    public function testSimpleValue($object) {
        $this->assertEquals($object->storage_values['intvalue'],$object->intvalue);
        return $object;
    }
}
