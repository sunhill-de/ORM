<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectReReadTest extends ObjectCommon
{
	/**
	 * @dataProvider FieldProvider
	 * @param string $classname
	 * @param array $init
	 * @param callback $init_callback
	 * @param array $modify
	 * @param callback $modify_callback
	 * @param array $expect
	 * @param callback $expect_callback
	 */
	public function testFields($classname,$init,$init_callback,$modify,$modify_callback,$expect,$expect_callback) {
	    $classname = 'Sunhill\\Test\\'.$classname;
	    $init_object = new $classname;
	    if (!is_null($init)) {
	        foreach ($init as $key => $value) {
	            if (is_array($value)) {
	                foreach ($value as $single_value) {
	                    $init_object->$key[] = $single_value;
	                }
	            } else {
	                $init_object->$key = $value;
	            }
	        }
	    }
	    if (!is_null($init_callback)) {
	        if (!$init_callback($init_object)) {
	            $this->fail("Init_Callback fehlgeschlagen.");
	        }
	    }
	    if (!is_null($init)) {
	        foreach ($init as $key => $value) {
	            $this->assertEquals($value,$init_object->$key,"Initialiserung von Feld '$key' fehlgeschlagen.");
	        }
	    }
	    $init_object->commit();
	    
	    $read_object = new $classname;
	    $read_object->load($init_object->get_id());
	    if (!is_null($init)) {
	        foreach ($init as $key => $value) {
	            $this->assertEquals($value,$read_object->$key,"Wiederauslesen von Feld '$key' fehlgeschlagen.");
	        }
	    }
	    if (!is_null($modify)) {
	        foreach ($modify as $key => $value) {
	            if (is_array($value)) {
	                foreach ($value as $single_value) {
	                    $read_object->$key[] = $single_value;
	                }
	            } else {
	                $read_object->$key = $value;
	            }	            
	        }
	    }
	    if (!is_null($modify_callback)) {
	        if (!$modify_callback($read_object)) {
	            $this->fail("Modify_Callback fehlgeschlagen.");
	        }
	    }
	    $read_object->commit();
	    $reread_object = new $classname;
	    $reread_object->load($init_object->get_id());
	    if (!is_null($expect)) {
	        foreach ($expect as $key => $value) {
	            $this->assertEquals($value,$reread_object->$key,"Wiederauslesen nach Modify von Feld '$key' fehlgeschlagen.");
	        }
	    }
	    if (!is_null($expect_callback)) {
	        if (!$expect_callback($read_object)) {
	            $this->fail("Expect_Callback fehlgeschlagen.");
	        }
	    }
	}
	
	public function FieldProvider() {
	    return [
            [ //Einfacher Test für einfache Felder
                'ts_testparent',
                [   'parentchar'=>'ABC',
                    'parentint'=>123,
                    'parentfloat'=>1.23,
                    'parenttext'=>'ABC DEF',
                    'parentdatetime'=>'2001-01-01 01:01:01',
                    'parentdate'=>'2011-01-01',
                    'parenttime'=>'11:11:11',
                    'parentenum'=>'testA'
                ],
                null,
                [   'parentchar'=>'DEF',
                    'parentint'=>456,
                    'parentfloat'=>4.56,
                    'parenttext'=>'GHI JKL',
                    'parentdatetime'=>'2002-02-02 02:02:02',
                    'parentdate'=>'2022-02-22',
                    'parenttime'=>'22:22:22',
                    'parentenum'=>'testB'
                ],
                null,
                [   'parentchar'=>'DEF',
                    'parentint'=>456,
                    'parentfloat'=>4.56,
                    'parenttext'=>'GHI JKL',
                    'parentdatetime'=>'2002-02-02 02:02:02',
                    'parentdate'=>'2022-02-22',
                    'parenttime'=>'22:22:22',
                    'parentenum'=>'testB'
                ],
                null
            ],
	        [ //Einfacher Test für geerbte Felder beide modifiziert
	            'ts_testchild',
	            [   'parentchar'=>'ABC',
	                'parentint'=>123,
	                'parentfloat'=>1.23,
	                'parenttext'=>'ABC DEF',
	                'parentdatetime'=>'2001-01-01 01:01:01',
	                'parentdate'=>'2011-01-01',
	                'parenttime'=>'11:11:11',
	                'parentenum'=>'testA',
                    'childchar'=>'CCC',
	                'childint'=>666,
	                'childfloat'=>3.33,
	                'childtext'=>'Lorem ipsum',
	                'childdatetime'=>'2001-02-22 22:01:22',
	                'childdate'=>'2011-02-02',
	                'childtime'=>'12:12:12',
	                'childenum'=>'testB'
	            ],
	            null,
	            [   'parentchar'=>'DEF',
	                'parentint'=>456,
	                'parentfloat'=>4.56,
	                'parenttext'=>'GHI JKL',
	                'parentdatetime'=>'2002-02-02 02:02:02',
	                'parentdate'=>'2022-02-22',
	                'parenttime'=>'22:22:22',
	                'parentenum'=>'testB',
                    'childchar'=>'DDD',
	                'childint'=>667,
	                'childfloat'=>3.43,
	                'childtext'=>'Sorem Lipsum',
	                'childdatetime'=>'2022-02-22 22:01:22',
	                'childdate'=>'2022-02-02',
	                'childtime'=>'12:22:12',
	                'childenum'=>'testC'
	            ],
	            null,
	            [   'parentchar'=>'DEF',
	                'parentint'=>456,
	                'parentfloat'=>4.56,
	                'parenttext'=>'GHI JKL',
	                'parentdatetime'=>'2002-02-02 02:02:02',
	                'parentdate'=>'2022-02-22',
	                'parenttime'=>'22:22:22',
	                'parentenum'=>'testB',
	                'childchar'=>'DDD',
	                'childint'=>667,
	                'childfloat'=>3.43,
	                'childtext'=>'Sorem Lipsum',
	                'childdatetime'=>'2022-02-22 22:01:22',
	                'childdate'=>'2022-02-02',
	                'childtime'=>'12:22:12',
	                'childenum'=>'testC'	                
	            ],
	            null
	        ],
	        [ //Einfacher Test für geerbte Felder nur Kinder modifiziert
	            'ts_testchild',
	            [   'parentchar'=>'ABC',
	                'parentint'=>123,
	                'parentfloat'=>1.23,
	                'parenttext'=>'ABC DEF',
	                'parentdatetime'=>'2001-01-01 01:01:01',
	                'parentdate'=>'2011-01-01',
	                'parenttime'=>'11:11:11',
	                'parentenum'=>'testA',
	                'childchar'=>'CCC',
	                'childint'=>666,
	                'childfloat'=>3.33,
	                'childtext'=>'Lorem ipsum',
	                'childdatetime'=>'2001-02-22 22:01:22',
	                'childdate'=>'2011-02-02',
	                'childtime'=>'12:12:12',
	                'childenum'=>'testB'
	            ],
	            null,
	            [   'childchar'=>'DDD',
	                'childint'=>667,
	                'childfloat'=>3.43,
	                'childtext'=>'Sorem Lipsum',
	                'childdatetime'=>'2022-02-22 22:01:22',
	                'childdate'=>'2022-02-02',
	                'childtime'=>'12:22:12',
	                'childenum'=>'testC'
	            ],
	            null,
	            [   'parentchar'=>'ABC',
	                'parentint'=>123,
	                'parentfloat'=>1.23,
	                'parenttext'=>'ABC DEF',
	                'parentdatetime'=>'2001-01-01 01:01:01',
	                'parentdate'=>'2011-01-01',
	                'parenttime'=>'11:11:11',
	                'parentenum'=>'testA',
	                'childchar'=>'DDD',
	                'childint'=>667,
	                'childfloat'=>3.43,
	                'childtext'=>'Sorem Lipsum',
	                'childdatetime'=>'2022-02-22 22:01:22',
	                'childdate'=>'2022-02-02',
	                'childtime'=>'12:22:12',
	                'childenum'=>'testC'
	            ],
	            null
	        ],
	        [ // Passthrutest
	            'ts_secondlevelchild',
	            [   'parentchar'=>'ABC',
	                'parentint'=>123,
	                'parentfloat'=>1.23,
	                'parenttext'=>'ABC DEF',
	                'parentdatetime'=>'2001-01-01 01:01:01',
	                'parentdate'=>'2011-01-01',
	                'parenttime'=>'11:11:11',
	                'parentenum'=>'testA',
	                'childint'=>678
	            ],
	            null,
	            [   'childint'=>1314
	            ],
	            null,
	            [   'parentchar'=>'ABC',
	                'parentint'=>123,
	                'parentfloat'=>1.23,
	                'parenttext'=>'ABC DEF',
	                'parentdatetime'=>'2001-01-01 01:01:01',
	                'parentdate'=>'2011-01-01',
	                'parenttime'=>'11:11:11',
	                'parentenum'=>'testA',
	                'childint'=>1314
	            ],
	            null
	        ],
	        
	    ];
	}
}
