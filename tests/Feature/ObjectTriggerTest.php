<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectTriggerTest extends ObjectCommon
{
    /**
     * @dataProvider TriggerProvider
     * @param unknown $init
     * @param unknown $change
     * @param unknown $expect
     */
    public function testTriggers($class,$init,$change,$expect) {
        $object = new $class;
        $object->trigger_exception = true;
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
            $this->assertEquals($expect,$object->flag);
        }
    }
    
    public function TriggerProvider() {
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
    
}
