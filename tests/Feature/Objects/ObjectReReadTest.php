<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\ReferenceOnly;

class ObjectReReadTest extends DBTestCase
{
        
    /**
	 * @dataProvider SimpleFieldProvider
	 * @param string $classname
	 * @param array $init
	 * @param array $modify
	 * @param array $expect
	 * @group simple
	 */
	public function testSimpleFields($classname,$init,$modify,$expect) {
	    Objects::flushCache();
	    $classname = 'Sunhill\\ORM\\Tests\\Objects\\'.$classname;
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
	    if (!is_null($init)) {
	        foreach ($init as $key => $value) {
	            $this->assertEquals($value,$init_object->$key,"Initialiserung von Feld '$key' fehlgeschlagen.");
	        }
	    }
	    $init_object->commit();
	    Objects::flushCache();
	    
// Read
	    $read_object = Objects::load($init_object->getID());
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
	    $read_object->commit();

	    Objects::flushCache();
	    $reread_object = Objects::load($init_object->getID());
	    if (!is_null($expect)) {
	        foreach ($expect as $key => $value) {
	            $this->assertEquals($value,$reread_object->$key,"Wiederauslesen nach Modify von Feld '$key' fehlgeschlagen.");
	        }
	    }
	}
	
	public function SimpleFieldProvider() {
	    return [ 
            [ //Einfacher Test für einfache Felder
                'TestParent',
                [   'parentchar'=>'ABC',
                    'parentint'=>123,
                    'parentfloat'=>1.23,
                    'parenttext'=>'ABC DEF',
                    'parentdatetime'=>'2001-01-01 01:01:01',
                    'parentdate'=>'2011-01-01',
                    'parenttime'=>'11:11:11',
                    'parentenum'=>'testA'
                ],
                [   'parentchar'=>'DEF',
                    'parentint'=>456,
                    'parentfloat'=>4.56,
                    'parenttext'=>'GHI JKL',
                    'parentdatetime'=>'2002-02-02 02:02:02',
                    'parentdate'=>'2022-02-22',
                    'parenttime'=>'22:22:22',
                    'parentenum'=>'testB'
                ],
                [   'parentchar'=>'DEF',
                    'parentint'=>456,
                    'parentfloat'=>4.56,
                    'parenttext'=>'GHI JKL',
                    'parentdatetime'=>'2002-02-02 02:02:02',
                    'parentdate'=>'2022-02-22',
                    'parenttime'=>'22:22:22',
                    'parentenum'=>'testB'
                ],
            ],
	        [ //Einfacher Test für geerbte Felder beide modifiziert
	            'TestChild',
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
	        ],
	        [ //Einfacher Test für geerbte Felder nur Kinder modifiziert
	            'TestChild',
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
	            [   'childchar'=>'DDD',
	                'childint'=>667,
	                'childfloat'=>3.43,
	                'childtext'=>'Sorem Lipsum',
	                'childdatetime'=>'2022-02-22 22:01:22',
	                'childdate'=>'2022-02-02',
	                'childtime'=>'12:22:12',
	                'childenum'=>'testC'
	            ],
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
	        ],
	        [ // Passthrutest
	            'SecondLevelChild',
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
	            [   'childint'=>1314
	            ],
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
	        ]];
	}

	/**
	 * @dataProvider ComplexFieldProvider
	 * @param string $classname
	 * @param array $init
	 * @param callback $init_callback
	 * @param array $modify
	 * @param callback $modify_callback
	 * @param array $expect
	 * @param callback $expect_callback
	 * @group complex
	 */
	public function testComplexFields($classname,$init,$init_callback,$read_callback,$modify_callback,$expect_callback) {
	    Objects::flushCache();
	    $classname = 'Sunhill\\ORM\\Tests\\Objects\\'.$classname;
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
	    $init_object->commit(); 
	    $id = $init_object->getID();
	    Objects::flushCache();
	    // Read
	    $read_object = Objects::load($id);
	    if (!is_null($read_callback)) {
	        if (!$read_callback($read_object)) {
	            $this->fail("Read_Callback fehlgeschlagen.");
	        }
	    }
	    
	    if (!is_null($modify_callback)) {
	        if (!$modify_callback($read_object)) {
	            $this->fail("Modify_Callback fehlgeschlagen.");
	        }
	    }
	    $read_object->commit();

	    Objects::flushCache();
	    $reread_object = Objects::load($init_object->getID());
	    if (!is_null($expect_callback)) {
	        if (!$expect_callback($reread_object)) {
	            $this->fail("Expect_Callback fehlgeschlagen.");
	        }
	    }
	    $this->assertTrue(true); // Damit nicht Risky
	}
	
	public function ComplexFieldProvider() {
	    return [ 
	        [ // Einfacher Test mit Komplexen-Felder
	            'TestParent',
	            [   'parentchar'=>'ABC',
	                'parentint'=>123,
	                'parentfloat'=>1.23,
	                'parenttext'=>'ABC DEF',
	                'parentdatetime'=>'2001-01-01 01:01:01',
	                'parentdate'=>'2011-01-01',
	                'parenttime'=>'11:11:11',
	                'parentenum'=>'testA'
	            ],
	            function($object) {
	                $add1 = new Dummy();
	                $add2 = new Dummy();
	                $add3 = new Dummy();
	                $add1->dummyint = 1234;
	                $add2->dummyint = 2345;
	                $add3->dummyint = 3456;
	                $object->parentobject = $add1;
	                $object->parentoarray[] = $add2;
	                $object->parentoarray[] = $add3;
	                $object->parentsarray[] = 'CBA';
	                $object->parentsarray[] = 'DCB';
	                return ($object->parentobject->dummyint == 1234) &&
	                ($object->parentoarray[0]->dummyint == 2345) &&
	                ($object->parentoarray[count($object->parentoarray)-1]->dummyint == 3456) &&
	                ($object->parentsarray[0] == 'CBA') &&
	                ($object->parentsarray[count($object->parentsarray)-1] == 'DCB');
	            },
	            function($object) {
	                return ($object->parentobject->dummyint == 1234) &&
	                ($object->parentoarray[0]->dummyint == 2345) &&
	                ($object->parentoarray[count($object->parentoarray)-1]->dummyint == 3456) &&
	                ($object->parentsarray[0] == 'CBA') &&
	                ($object->parentsarray[count($object->parentsarray)-1] == 'DCB');
	            },
	            function($object) {
	                $add1 = new Dummy();
	                $add2 = new Dummy();
	                $add1->dummyint = 4321;
	                $add2->dummyint = 5432;
	                $object->parentobject = $add1;
	                $object->parentoarray[] = $add2;
	                $object->parentsarray[] = 'EDC';
	                return ($object->parentobject->dummyint == 4321) &&
	                ($object->parentoarray[0]->dummyint == 2345) &&
	                ($object->parentoarray[count($object->parentoarray)-1]->dummyint == 5432) &&
	                ($object->parentsarray[0] == 'CBA') &&
	                ($object->parentsarray[count($object->parentsarray)-1] == 'EDC');
	            },
	            function($object) { // Expect-Callback
	                $this->assertEquals([4321,2345,3456,5432,'CBA','DCB','EDC'],[
	                    $object->parentobject->dummyint,
	                    $object->parentoarray[0]->dummyint,
	                    $object->parentoarray[1]->dummyint,
	                    $object->parentoarray[count($object->parentoarray)-1]->dummyint,
	                    $object->parentsarray[0],
	                    $object->parentsarray[1],
	                    $object->parentsarray[count($object->parentsarray)-1]]
	                    );
	                return true;
	            },
	            ],
	            [ //Einfacher Test für geerbte Felder beide modifiziert
	                'TestChild',
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
	                function($object) {
	                    $add1 = new Dummy();
	                    $add2 = new Dummy();
	                    $add3 = new Dummy();
	                    $add4 = new Dummy();
	                    $add5 = new Dummy();
	                    $add6 = new Dummy();
	                    $add1->dummyint = 1234;
	                    $add2->dummyint = 2345;
	                    $add3->dummyint = 3456;
	                    $add4->dummyint = 4567;
	                    $add5->dummyint = 5678;
	                    $add6->dummyint = 6789;
	                    $object->parentobject = $add1;
	                    $object->parentoarray[] = $add2;
	                    $object->parentoarray[] = $add3;
	                    $object->childobject = $add4;
	                    $object->childoarray[] = $add5;
	                    $object->childoarray[] = $add6;
	                    $object->parentsarray[] = 'CBA';
	                    $object->parentsarray[] = 'DCB';
	                    $object->childsarray[] = 'EDC';
	                    $object->childsarray[] = 'FED';
	                    return ($object->parentobject->dummyint == 1234) &&
	                    ($object->parentoarray[0]->dummyint == 2345) &&
	                    ($object->parentoarray[count($object->parentoarray)-1]->dummyint == 3456) &&
	                    ($object->parentsarray[0] == 'CBA') &&
	                    ($object->parentsarray[count($object->parentsarray)-1] == 'DCB') && 
	                    ($object->childobject->dummyint == 4567) &&
	                    ($object->childoarray[0]->dummyint == 5678) &&
	                    ($object->childoarray[count($object->childoarray)-1]->dummyint == 6789) &&
	                    ($object->childsarray[0] == 'EDC') &&
	                    ($object->childsarray[count($object->childsarray)-1] == 'FED');
	                },
	                function($object) {
	                    return ($object->parentobject->dummyint == 1234) &&
	                    ($object->parentoarray[0]->dummyint == 2345) &&
	                    ($object->parentoarray[count($object->parentoarray)-1]->dummyint == 3456) &&
	                    ($object->parentsarray[0] == 'CBA') &&
	                    ($object->parentsarray[count($object->parentsarray)-1] == 'DCB') &&
	                    ($object->childobject->dummyint == 4567) &&
	                    ($object->childoarray[0]->dummyint == 5678) &&
	                    ($object->childoarray[count($object->childoarray)-1]->dummyint == 6789) &&
	                    ($object->childsarray[0] == 'EDC') &&
	                    ($object->childsarray[count($object->childsarray)-1] == 'FED');
	                },
	                function($object) {
	                    $add1 = new Dummy();
	                    $add2 = new Dummy();
	                    $add3 = new Dummy();
	                    $add4 = new Dummy();
	                    $add1->dummyint = 4321;
	                    $add2->dummyint = 5432;
	                    $add3->dummyint = 6543;
	                    $add4->dummyint = 7654;
	                    $object->parentobject = $add1;
	                    $object->parentoarray[] = $add2;
	                    $object->parentsarray[] = 'PQR';
	                    $object->childobject = $add3;
	                    $object->childoarray[] = $add4;
	                    $object->childsarray[] = 'QRS';
	                    
	                    
	                    return ($object->parentobject->dummyint == 4321) &&
	                    ($object->parentoarray[0]->dummyint == 2345) &&
	                    ($object->parentoarray[count($object->parentoarray)-1]->dummyint == 5432) &&
	                    ($object->parentsarray[0] == 'CBA') &&
	                    ($object->parentsarray[count($object->parentsarray)-1] == 'PQR') &&
	                    ($object->childobject->dummyint == 6543) &&
	                    ($object->childoarray[0]->dummyint == 5678) &&
	                    ($object->childoarray[count($object->parentoarray)-1]->dummyint == 7654) &&
	                    ($object->childsarray[0] == 'EDC') &&
	                    ($object->childsarray[count($object->parentsarray)-1] == 'QRS');
	                },
	                function($object) { // Expect-Callback
	                    $this->assertEquals(
	                        [
	                            4321,2345,5432,'CBA','PQR',6543,5678,7654,'EDC','QRS'
	                        ],[
	                            $object->parentobject->dummyint,
	                            $object->parentoarray[0]->dummyint,
	                            $object->parentoarray[count($object->parentoarray)-1]->dummyint,
	                            $object->parentsarray[0],
	                            $object->parentsarray[count($object->parentsarray)-1],
	                            $object->childobject->dummyint,
	                            $object->childoarray[0]->dummyint,
	                            $object->childoarray[count($object->parentoarray)-1]->dummyint,
	                            $object->childsarray[0],
	                            $object->childsarray[count($object->parentsarray)-1]
	                        ]
	                        );
	                    return true;
	                },
	                ],
	                [ // Änderung nur der untergebenen Objekte, hier mit doppelter Referenz
	                    'ReferenceOnly',
	                    ['testint'=>1234],
	                    function($object) {
	                        $add1 = new Dummy();
	                        $add1->dummyint = 4321;
	                        $object->testobject = $add1;
	                        $object->testoarray[] = $add1;
	                        return true;
	                    },
	                    function($object) { // Read-Callback
	                        return ($object->testint == 1234) && ($object->testobject->dummyint == 4321) &&
	                               ($object->testoarray[0]->dummyint == 4321);
	                    },
	                    function($object) { // Modify Callback
	                        $object->testoarray[0]->dummyint = 666;
	                        return true;
	                    },
	                    function($object) { // Expect Callback
	                        $this->assertEquals(
	                            [
	                             1234,666,666   
	                            ],[
	                                $object->testint,
	                                $object->testobject->dummyint,
	                                $object->testoarray[0]->dummyint
	                            ]	                            
	                            );
	                        
	                        return true;
	                    }
	                    
	                ],
	                [ // Zirkuläre Referenzen
	                    'ReferenceOnly',
	                    ['testint'=>1234],
	                    function($object) {
	                        $add1 = new ReferenceOnly();
	                        $add1->testint = 4321;
	                        $add1->testobject = $object;
	                        $object->testobject = $add1;
	                        return true;
	                    },
	                    function($object) { // Read-Callback
	                        return ($object->testint == 1234) && ($object->testobject->testint == 4321) &&
	                        ($object->testobject->testobject->testint == 1234);
	                    },
	                    function($object) { // Modify Callback
	                        $object->testobject->testint = 666;
	                        return true;
	                    },
	                    function($object) { // Expect Callback
	                        return ($object->testint == 1234) && ($object->testobject->testint == 666) &&
	                        ($object->testobject->testobject->testint == 1234);
	                    }
	                    
	                    ],
	                    
	                    [ // Austausch eines Objektes
	                        'ReferenceOnly',
	                        ['testint'=>1234],
	                        function($object) {
	                            $add1 = new Dummy();
	                            $add1->dummyint = 4321;
	                            $object->testobject = $add1;
	                            return true;
	                        },
	                        function($object) { // Read-Callback
	                            return ($object->testint == 1234) && ($object->testobject->dummyint == 4321);
	                        },
	                        function($object) { // Modify Callback
	                            $add1 = new Dummy();
	                            $add1->dummyint = 1111;
	                            $object->testobject = $add1;
	                            return true;
	                        },
	                        function($object) { // Expect Callback
	                            return ($object->testint == 1234) && ($object->testobject->dummyint == 1111);
	                        }
	                        
	                        ],
	                        
	                        [ // Löschen einer Referenz
	                        'ReferenceOnly',
	                        ['testint'=>1234],
	                        function($object) {
	                            $add1 = new Dummy();
	                            $add1->dummyint = 4321;
	                            $object->testobject = $add1;
	                            return true;
	                        },
	                        function($object) { // Read-Callback
	                            return true;
	                        },
	                        function($object) { // Modify Callback
	                            $object->testobject  = null;
	                            return true;
	                        },
	                        function($object) { // Expect Callback
	                            return (is_null($object->testobject));
	                        }
	                        
	                        ],
	                        [ // Einfaches Ändern eines untergeordneten Objektes
	                            'ReferenceOnly',
	                            ['testint'=>1234],
	                            function($object) {
	                                $add1 = new Dummy();
	                                $add1->dummyint = 4321;
	                                $object->testobject = $add1;
	                                return true;
	                            },
	                            function($object) { // Read-Callback
	                                return true;
	                            },
	                            function($object) { // Modify Callback
	                                $object->testobject->dummyint = 5432;
	                                return true;
	                            },
	                            function($object) { // Expect Callback
	                                $this->assertEquals(5432,$object->testobject->dummyint);
	                                return true;
	                            }
	                            
	                            ]
	                            
	                    
	            ];
	}
	
	public function testChildChange() {
	       Objects::flushCache();
	       $object = new ReferenceOnly();
	       $child  = new Dummy();
	       $object->testint = 123;
	       $child->dummyint = 234;
	       $object->testobject = $child;
	       $object->commit();
	       $child->dummyint = 666;
	       $child->commit();
	       $read = Objects::load($object->getID());
	       $this->assertEquals(666,$read->testobject->dummyint);
	       
	}
	
	/**
	 * @group many
	 */
	public function testManyObjects() {
	    Objects::flushCache();
	    $sub = array();
	    $main = array();
	    for ($i=0;$i<100;$i++) {
	        $sub[$i] = new Dummy();
	        $sub[$i]->dummyint = $i;
	        $main[$i] = new ReferenceOnly();
	        $main[$i]->testobject = $sub[$i];
	        $main[$i]->testint = $i+100;
	        $main[$i]->commit();
	        if ($i==50) {
	            $id = $main[$i]->getID();
	        }
	    }
	    $obj = Objects::load($id);
        $this->assertEquals(50,$obj->testobject->dummyint);
	}
}
