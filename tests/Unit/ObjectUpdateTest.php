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
            [ // Update einfacher Werte
                function($object) {
                    $object->intvalue = 234;
                },
                ['id'=>1,'intvalue'=>['FROM'=>123,'TO'=>234]]                
            ],
// Tag Tests            
            [ // Update von Tags: Hinzufügen
                function($object) {
                    $object->tags->stick(3);
                },
                ['id'=>1,'tags'=>['FROM'=>[1,2],'TO'=>[1,2,3],'ADD'=>[3],'DELETE'=>[]]]
            ],
            [ // Update von Tags: Löschen
                function($object) {
                    $object->tags->remove(2);
                },
                ['id'=>1,'tags'=>['FROM'=>[1,2],'TO'=>[1],'ADD'=>[],'DELETE'=>[2]]]
            ],
            [ // Update von Tags: Kombiniert
                function($object) {
                    $object->tags->stick(3);
                    $object->tags->remove(2);
                },
                ['id'=>1,'tags'=>['FROM'=>[1,2],'TO'=>[1,3],'ADD'=>[3],'DELETE'=>[2]]]
            ],
// Objectfeldtests            
            [
                function($object) {
                   $dummy = \Sunhill\Objects\oo_object::load_object_of(3);
                   $object->objectvalue = $dummy;
                },
                ['id'=>1,'objectvalue'=>['FROM'=>2,'TO'=>3]]
            ],
            [
                function($object) {
                    $object->objectvalue = null;
                },
                ['id'=>1,'objectvalue'=>['FROM'=>2,'TO'=>null]]
            ],
// Object Array Tests           
            [
                function($object) {
                    /**
                     * @todo Hier sollte ein Mock eingesetzt werden
                     */
                    $dummy1 = \Sunhill\Objects\oo_object::load_object_of(2);
                    $object->oarray[] = $dummy1;
                },
                ['id'=>1,'oarray'=>['FROM'=>[3,4],'TO'=>[3,4,2],'ADD'=>[2],'DELETE'=>[]]]
            ],
            [
                function($object) {
                    unset($object->oarray[1]);;
                },
                ['id'=>1,'oarray'=>['FROM'=>[3,4],'TO'=>[3],'ADD'=>[],'DELETE'=>[4]]]
            ],
            [
                function($object) {
                    /**
                     * @todo Hier sollte ein Mock eingesetzt werden
                     */
                    $dummy1 = \Sunhill\Objects\oo_object::load_object_of(2);
                    unset($object->oarray[1]);;
                    $object->oarray[] = $dummy1;
                },
                ['id'=>1,'oarray'=>['FROM'=>[3,4],'TO'=>[3,2],'ADD'=>[2],'DELETE'=>[4]]]
            ],
// Stringarray-Tests
            [
                function($object) {
                    $object->sarray[] = 'JKL';
                },
                ['id'=>1,'sarray'=>['FROM'=>['ABC','DEF','GHI'],'TO'=>['ABC','DEF','GHI','JKL'],'ADD'=>['JKL'],'DELETE'=>[]]]
            ],
            [
                function($object) {
                     unset($object->sarray[1]);;
                },
                ['id'=>1,'sarray'=>['FROM'=>['ABC','DEF','GHI'],'TO'=>['ABC','GHI'],'ADD'=>[],'DELETE'=>['DEF']]]
            ],
            [
                function($object) {
                     unset($object->sarray[1]);;
                     $object->sarray[] = 'JKL';
                },
                ['id'=>1,'sarray'=>['FROM'=>['ABC','DEF','GHI'],'TO'=>['ABC','GHI','JKL'],'ADD'=>['JKL'],'DELETE'=>['DEF']]]
            ],
// Attribut-Tests
            [
                function($object) {
                    $object->general_attribute = 1509;
                },
                ['id'=>1,'attributes'=>['general_attribute'=>[
                    'attribute_id'=>4,
                    'value_id'=>1,
                    'object_id'=>1,
                    'value'=>['FROM'=>12,'TO'=>1509],
                    'textvalue'=>['FROM'=>'','TO'=>''],
                    'name'=>'general_attribute',
                    'allowedobjects'=>"\\Sunhill\\Objects\\oo_object",
                    'type'=>'int',
                    'property'=>'']                       
                    ]                    
                ]
            ],
            [
                function($object) {
                    $object->general_attribute = null;
                },
                ['id'=>1,'attributes'=>['general_attribute'=>[
                    'attribute_id'=>4,
                    'value_id'=>1,
                    'object_id'=>1,
                    'value'=>['FROM'=>12,'TO'=>null],
                    'textvalue'=>['FROM'=>'','TO'=>null],
                    'name'=>'general_attribute',
                    'allowedobjects'=>"\\Sunhill\\Objects\\oo_object",
                    'type'=>'int',
                    'property'=>'']
                ]
                ]
            ],                
// Calculated            
            [
                function($object) {
                    $object->intvalue = 981;
                    $object->recalculate();
                },
                [
                    'id'=>1,
                    'intvalue'=>['FROM'=>123,'TO'=>981],
                    'calcvalue'=>['FROM'=>'123A','TO'=>'981A']
                ]
            ],
          ];
    }
}
