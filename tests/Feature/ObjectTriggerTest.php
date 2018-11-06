<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectTriggerTest extends ObjectCommon
{
    /**
     * @dataProvider SimpleTriggerProvider
     * @param unknown $init
     * @param unknown $change
     * @param unknown $expect
     */
    public function testSimpleTriggers($class,$init,$change,$expect) {
        $object = new $class;
        $object->trigger_exception = true;
        $object::$flag = '';
        foreach ($init as $key => $value) {
            $object->$key = $value;
        }
        $object->commit();
        foreach ($change as $key => $value) {
            $object->$key = $value;
        }
        if ($expect == 'EXCEPTION') {
            try {
                $object->commit();
            } catch (\exception $e) {
                $this->assertTrue(true);
                return;
            }
            $this->fail();
        } else {
            $object->commit();
            $this->assertEquals($expect,$object::$flag);
        }
    }
    
    public function SimpleTriggerProvider() {
        return [
          [ 'Sunhill\\Test\\ts_testparent',
            [   'parentchar'=>'ABC',
                'parentint'=>123,
                'parentfloat'=>1.23,
                'parenttext'=>'ABC DEF',
                'parentdatetime'=>'2001-01-01 01:01:01',
                'parentdate'=>'2011-01-01',
                'parenttime'=>'11:11:11',
                'parentenum'=>'testA'
            ],
            [
                'parentint'=>234                
            ],
            'BINT(123=>234)AINT(123=>234)'
          ],
            [ 'Sunhill\\Test\\ts_testparent',
                [   'parentchar'=>'ABC',
                    'parentint'=>123,
                    'parentfloat'=>1.23,
                    'parenttext'=>'ABC DEF',
                    'parentdatetime'=>'2001-01-01 01:01:01',
                    'parentdate'=>'2011-01-01',
                    'parenttime'=>'11:11:11',
                    'parentenum'=>'testA'
                ],
                [
                    'parentchar'=>'BCD'
                ],
                'BCHAR(ABC=>BCD)AINT(123=>124)ACHAR(ABC=>BCD)' // @todo Eigentlich dürfte man sich nicht auf die Reihenfolge verlassen dürfen
            ],
            [ 'Sunhill\\Test\\ts_testparent',
                [   'parentchar'=>'ABC',
                    'parentint'=>123,
                    'parentfloat'=>1.23,
                    'parenttext'=>'ABC DEF',
                    'parentdatetime'=>'2001-01-01 01:01:01',
                    'parentdate'=>'2011-01-01',
                    'parenttime'=>'11:11:11',
                    'parentenum'=>'testA'
                ],
                [
                    'parentint'=>234
                ],
                'BINT(123=>234)AINT(123=>234)'
            ],
            [ 'Sunhill\\Test\\ts_testparent',
                [   'parentchar'=>'ABC',
                    'parentint'=>123,
                    'parentfloat'=>1.23,
                    'parenttext'=>'ABC DEF',
                    'parentdatetime'=>'2001-01-01 01:01:01',
                    'parentdate'=>'2011-01-01',
                    'parenttime'=>'11:11:11',
                    'parentenum'=>'testA'
                ],
                [
                    'parentfloat'=>2.34
                ],
                'EXCEPTION'
            ]
            
        ];
    }
    
    /**
     * @dataProvider ComplexTriggerProvider
     * @param unknown $init
     * @param unknown $change
     * @param unknown $expect
     * @group complex
     */
    public function testComplexTriggers($class,$init,$change,$expect) {
        $object = new $class;
        $object->trigger_exception = true;
        $object::$flag = '';
        $init($object);
        $object->commit(); 
        $change($object);
        if ($expect == 'EXCEPTION') {
            try {
                $object->commit();
            } catch (\exception $e) {
                $this->assertTrue(true);
                return;
            }
            $this->fail();
        } else {
            $object->commit(); 
            $this->assertEquals($expect,$object::$flag);
        }
    }
    
    public function ComplexTriggerProvider() {
        return [
            [ 'Sunhill\\Test\\ts_testparent',
                function($object) {
                    $object->parentchar='ABC';
                    $object->parentint=123;
                    $object->parentfloat=1.23;
                    $object->parenttext='ABC DEF';
                    $object->parentdatetime='2001-01-01 01:01:01';
                    $object->parentdate='2011-01-01';
                    $object->parenttime='11:11:11';
                    $object->parentenum='testA';
                    $add = new \Sunhill\Test\ts_dummy();
                    $add->dummyint = 666;
                    $object->parentobject = $add;
                },
                function($object) {
                    $object->parentobject->dummyint = 777;
                },
                'AOBJECT(dummyint:666=>777)'
            ]
            
        ];
    }
    
}
