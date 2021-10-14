<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Objects\ts_dummy;

class ObjectInsertTest extends DBTestCase
{
    
    /**
     * @dataProvider InsertProvider
     * @group insert
     * @param unknown $class
     * @param unknown $fields
     * @param unknown $callback
     * @param unknown $test
     * @param unknown $expected
     */
    public function testInsertObject($class,$fields,$callback,$test,$expected) {
        $object = new $class();
        foreach ($fields as $field=>$value) {
            $object->$field = $value;
        }
        if (!is_null($callback)) {
            $callback($object);
        }
        $object->commit();
        
        $read = Objects::load($object->getID());
        $this->assertEquals($expected,$this->get_field($read, $test));
    }
    
    public function InsertProvider() {
        return [
            ["\\Sunhill\\ORM\\Tests\\Objects\\ts_dummy",['dummyint'=>666],null,'dummyint',666],
            ["\\Sunhill\\ORM\\Tests\\Objects\\ts_dummy",['dummyint'=>666],function($object){
                $object->tags->stick('TagA');
                $object->tags->stick('TagB');
            },'tags[1]','TagB'],
            ["\\Sunhill\\ORM\\Tests\\Objects\\ts_dummy",['dummyint'=>666],function($object){
                $object->int_attribute = 898;
            },'int_attribute',898],
            ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testparent',
            [   'parentchar'=>'ABC',
                'parentint'=>123,
                'parentfloat'=>1.23,
                'parenttext'=>'ABC DEF',
                'parentdatetime'=>'2001-01-01 01:01:01',
                'parentdate'=>'2011-01-01',
                'parenttime'=>'11:11:11',
                'parentenum'=>'testA'
            ],null,'parentchar','ABC'],
            
            ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testparent',
            [   'parentchar'=>'ABC',
                'parentint'=>123,
                'parentfloat'=>1.23,
                'parenttext'=>'ABC DEF',
                'parentdatetime'=>'2001-01-01 01:01:01',
                'parentdate'=>'2011-01-01',
                'parenttime'=>'11:11:11',
                'parentenum'=>'testA'
            ],function($object){
                $dummy = new ts_dummy();
                $dummy->dummyint = 333;
                $object->parentobject = $dummy;
            },'parentobject->dummyint',333],
            ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testparent',
                [   'parentchar'=>'ABC',
                    'parentint'=>123,
                    'parentfloat'=>1.23,
                    'parenttext'=>'ABC DEF',
                    'parentdatetime'=>'2001-01-01 01:01:01',
                    'parentdate'=>'2011-01-01',
                    'parenttime'=>'11:11:11',
                    'parentenum'=>'testA'
                ],function($object){
                    $dummy1 = new ts_dummy();
                    $dummy1->dummyint = 333;
                    $dummy2 = new ts_dummy();
                    $dummy2->dummyint = 444;
                    $object->parentoarray[] = $dummy1;
                    $object->parentoarray[] = $dummy2;
                },'parentoarray[1]->dummyint',444],
                ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testparent',
                    [   'parentchar'=>'ABC',
                        'parentint'=>123,
                        'parentfloat'=>1.23,
                        'parenttext'=>'ABC DEF',
                        'parentdatetime'=>'2001-01-01 01:01:01',
                        'parentdate'=>'2011-01-01',
                        'parenttime'=>'11:11:11',
                        'parentenum'=>'testA'
                    ],function($object){
                        $object->parentsarray[] = 'E1';
                        $object->parentsarray[] = 'E2';
                    },'parentsarray[1]','E2'],
                   
                    ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testchild',
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
                    ],null,'childdate','2011-02-02'],
                    
                    ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testchild',
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
                        ],function($object){
                            $object->parentsarray[] = 'E1';
                            $object->parentsarray[] = 'E2';
                            $object->childsarray[] = 'CE1';
                            $object->childsarray[] = 'CE2';
                        },'childsarray[1]','CE2'],
                        ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testchild',
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
                            ],function($object){
                                $object->parentsarray[] = 'E1';
                                $object->parentsarray[] = 'E2';
                                $object->childsarray[] = 'CE1';
                                $object->childsarray[] = 'CE2';
                            },'parentsarray[1]','E2'],
                            
                            ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testchild',
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
                                ],function($object){
                                    $dummy1 = new ts_dummy();
                                    $dummy1->dummyint = 1111;
                                    $dummy2 = new ts_dummy();
                                    $dummy2->dummyint = 2222;
                                    $dummy3 = new ts_dummy();
                                    $dummy3->dummyint = 3333;
                                    $dummy4 = new ts_dummy();
                                    $dummy4->dummyint = 4444;
                                    $object->parentoarray[] = $dummy1;
                                    $object->parentoarray[] = $dummy2;
                                    $object->childoarray[] = $dummy3;
                                    $object->childoarray[] = $dummy4;
                                },'parentoarray[1]->dummyint',2222],
                                
                                ['\\Sunhill\\ORM\\Tests\\Objects\\ts_testchild',
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
                                    ],function($object){
                                        $dummy1 = new ts_dummy();
                                        $dummy1->dummyint = 1111;
                                        $dummy2 = new ts_dummy();
                                        $dummy2->dummyint = 2222;
                                        $dummy3 = new ts_dummy();
                                        $dummy3->dummyint = 3333;
                                        $dummy4 = new ts_dummy();
                                        $dummy4->dummyint = 4444;
                                        $object->parentoarray[] = $dummy1;
                                        $object->parentoarray[] = $dummy2;
                                        $object->childoarray[] = $dummy3;
                                        $object->childoarray[] = $dummy4;
                                    },'childoarray[1]->dummyint',4444],
                                    ];
    }
    
    protected function get_field($loader,$fieldname) {
        $match = '';
        if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            $index = $match['index'];
            return $loader->$name[$index]->$subfield;
        } else if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]\[(?P<index2>\w+)\]/',$fieldname,$match)) {
            $name = $match['name'];
            $index2 = $match['index2'];
            $index = $match['index'];
            return $loader->$name[$index][$index2];
        } else if (preg_match('/(?P<name>\w+)->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            return $loader->$name->$subfield;
        } if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]/',$fieldname,$match)){
            $name = $match['name'];
            $index = $match['index'];
            return $loader->$name[$index];
        }  else {
            return $loader->$fieldname;
        }
    }
    
}
