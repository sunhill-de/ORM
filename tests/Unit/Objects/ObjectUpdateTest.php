<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\DBTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Objects\ObjectUnit;

class ObjectUpdateTest extends DBTestCase
{
    
    /**
     * @dataProvider UpdateProvider
     * @param unknown $update_callback
     * @param unknown $test_callback
     */
    public function testStorageUpdate($update_callback,$expectations) {
        Objects::flushCache();
        $object = new ObjectUnit();
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
                'allowedobjects'=>"\\Sunhill\\Objects\\ORMObject",
                'type'=>'int',
                'property'=>''
            ]]
        ];
        $object->publicLoad(1);
        $object->storage_values = [];
        $update_callback($object);
        $object->commit();
        foreach ($expectations as $query) {
            $this->assertEquals($query[2],$object->storage_values[$query[0]][$query[1]]);
        }
    }
    
    public function UpdateProvider() {
        return [
            [ // #1: Update einfacher Werte
                function($object) {
                    $object->intvalue = 234;
                },
                [
                    ['intvalue','FROM',123],
                    ['intvalue','TO',234]
                ]
            ],
            
            // Tag Tests            
            [ // #2: Update von Tags: Hinzufügen
                function($object) {
                    $object->tags->stick(3);
                },
                [
                    ['tags','FROM',[1,2]],
                    ['tags','TO',[1,2,3]],
                    ['tags','ADD',[2=>3]],
                    ['tags','DELETE',[]],
                    ['tags','NEW',[3]],
                    ['tags','REMOVED',[]]
                ]
            ],

            [ // #3: Update von Tags: Löschen
                function($object) {
                    $object->tags->remove(2);
                },
                [
                    ['tags','FROM',[1,2]],
                    ['tags','TO',[1]],
                    ['tags','ADD',[]],
                    ['tags','DELETE',[1=>2]],
                    ['tags','NEW',[]],
                    ['tags','REMOVED',[2]]
                ]
            ],
            [ // #4: Update von Tags: Kombiniert
                function($object) {
                    $object->tags->stick(3);
                    $object->tags->remove(2);
                },
                [
                    ['tags','FROM',[1,2]],
                    ['tags','TO',[1,3]],
                    ['tags','NEW',[3]],
                    ['tags','REMOVED',[2]],
                    ['tags','ADD',[1=>3]],
                    ['tags','DELETE',[1=>2]]
                ]
            ],

// Objectfeldtests            
             [ // #5: Ändern eines Objektfelde 
                    function($object) {
                        $dummy = Objects::load(3);
                        $object->objectvalue = $dummy;
                    },
                    [
                        ['objectvalue','FROM',2],
                        ['objectvalue','TO',3]
                    ]
            ],
            [ // #6: Löschen eines Objektfeldes (setzen mit null)
                    function($object) {
                        $object->objectvalue = null;
                     },
                     [
                         ['objectvalue','FROM',2],
                         ['objectvalue','TO',null]
                     ]
            ],

// Object Array Tests           
              [ // #7
                    function($object) {
                             /**
                              * @todo Hier sollte ein Mock eingesetzt werden
                              */
                             $dummy1 = Objects::load(2);
                             $object->oarray[] = $dummy1;
                    },
                    [
                        ['oarray','FROM',[3,4]],
                        ['oarray','TO',[3,4,2]],
                        ['oarray','ADD',[2=>2]],
                        ['oarray','DELETE',[]],
                        ['oarray','NEW',[2]],
                        ['oarray','REMOVED',[]],
                    ]
                        
            ],
            [
                function($object) {
                    /**
                     * @todo Hier sollte ein Mock eingesetzt werden
                     */
                    unset($object->oarray[1]);
                },
                [
                    ['oarray','FROM',[3,4]],
                    ['oarray','TO',[3]],
                    ['oarray','ADD',[]],
                    ['oarray','DELETE',[1=>4]],
                    ['oarray','NEW',[]],
                    ['oarray','REMOVED',[4]],
                ]
            ],
            [
                function($object) {
                    /**
                     * @todo Hier sollte ein Mock eingesetzt werden
                     */
                    $dummy1 = Objects::load(2);
                    unset($object->oarray[1]);;
                    $object->oarray[] = $dummy1;
                },
                [
                    ['oarray','FROM',[3,4]],
                    ['oarray','TO',[3,2]],
                    ['oarray','ADD',[1=>2]],
                    ['oarray','DELETE',[1=>4]],
                    ['oarray','NEW',[2]],
                    ['oarray','REMOVED',[4]]
                ]
            ],
            
// Stringarray-Tests
            [
                function($object) {
                    $object->sarray[] = 'JKL';
                },
                [
                    ['sarray','FROM',['ABC','DEF','GHI']],
                    ['sarray','TO',['ABC','DEF','GHI','JKL']],
                    ['sarray','ADD',[3=>'JKL']],
                    ['sarray','DELETE',[]]
                ]
            ],
            [
                function($object) {
                     unset($object->sarray[1]);;
                },
                [
                    ['sarray','FROM',['ABC','DEF','GHI']],
                    ['sarray','TO',['ABC','GHI']],
                    ['sarray','ADD',[]],
                    ['sarray','DELETE',[1=>'DEF']]
                ]
            ],
            [
                function($object) {
                     unset($object->sarray[1]);;
                     $object->sarray[] = 'JKL';
                },
                [
                    ['sarray','FROM',['ABC','DEF','GHI']],
                    ['sarray','TO',['ABC','GHI','JKL']],
                    ['sarray','ADD',[2=>'JKL']],
                    ['sarray','DELETE',[1=>'DEF']]
                ]
            ],
// Calculated            
            [
                function($object) {
                    $object->intvalue = 981;
                    $object->recalculate();
                },
                [
                    ['intvalue','FROM',123],
                    ['intvalue','TO',981],
                    ['calcvalue','FROM','123A'],
                    ['calcvalue','TO','981A']
                ]
            ],
          ];
    }
    
    public function testAttribute1() {
        Objects::flushCache();
        $object = new ObjectUnit();
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
                'allowedobjects'=>"\\Sunhill\\Objects\\ORMObject",
                'type'=>'int',
                'property'=>''
            ]]
        ];
        $object->publicLoad(1);
        $object->storage_values = [];
        $object->general_attribute = 1509;
        $object->commit();
        $this->assertEquals(12,$object->storage_values['attributes']['general_attribute']['value']['FROM']);        
        $this->assertEquals(1509,$object->storage_values['attributes']['general_attribute']['value']['TO']);        
    }
        
    public function testAttribute2() {
        Objects::flushCache();
        $object = new ObjectUnit();
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
                'allowedobjects'=>"\\Sunhill\\Objects\\ORMObject",
                'type'=>'int',
                'property'=>''
            ]]
        ];
        $object->publicLoad(1);
        $object->storage_values = [];
        $object->general_attribute = null;
        $object->commit();
        $this->assertEquals(12,$object->storage_values['attributes']['general_attribute']['value']['FROM']);
        $this->assertEquals(null,$object->storage_values['attributes']['general_attribute']['value']['TO']);
    }
}
