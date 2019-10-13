<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class ObjectUpdateTest extends \Tests\sunhill_testcase_db
{

    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_special_table('passthrus');
        $this->create_special_table('testparents');
        $this->create_special_table('testchildren');
        $this->create_special_table('referenceonlies');
    }
    
    protected function prepare_read() {
        $this->prepare_tables();
        $this->create_load_scenario();
    }
    
    /**
     * @dataProvider UpdateProvider
     * @param unknown $update_callback
     * @param unknown $test_callback
     */
    public function testStorageUpdate($update_callback,$test_result) {
        $this->prepare_read();
        $object = new \Sunhill\Test\ts_objectunit();
        $object->storage_values = [
            'id'=>1,
            'created_at'=>'2019-10-06 12:05:00',
            'modified_at'=>'2019-10-06 12:05:00',
            'intvalue'=>123,
            'objectvalue'=>2,
            'sarray'=>['ABC','DEF','GHI'],
            'oarray'=>[3,4],
            'calcvalue'=>'123A',
            'tags'=>[1,2],
            'attributes'=>['general_attribute'=>[
                'attribute_id'=>4,
                'value_id'=>1,
                'object_id'=>1,
                'value'=>12,
                'textvalue'=>'',
                'name'=>'general_attribute',
                'allowedobjects'=>"\\Sunhill\\Objects\\oo_object",
                'type'=>'int',
                'property'=>''
            ]]
        ];
        $object->public_load(1);
        $object->storage_values = [];
        $update_callback($object);
        $object->commit();
        $this->assertEquals($test_result,$object->storage_values);
    }
    
    public function UpdateProvider() {
        return [
            [
                function($object) {
                    $object->intvalue = 234;
                },
                ['id'=>1,'intvalue'=>234]                
            ],
            [
                function($object) {
                    $object->tags->stick(3);
                },
                ['id'=>1,'tags'=>['add'=>[3],'remove'=>[]]]
            ],
            [
                function($object) {
                    $object->tags->remove(2);
                },
                ['id'=>1,'tags'=>['add'=>[],'remove'=>[2]]]
            ],
            [
                function($object) {
                    $object->stick(3);
                    $object->tags->remove(2);
                },
                ['id'=>1,'tags'=>['add'=>[3],'remove'=>[2]]]
            ],
                
    ];
    }
}
