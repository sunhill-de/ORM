<?php

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Search\query_builder;
use Sunhill\ORM\Search\query_atom;

class QueryBuilderTest extends TestCase
{
    public function testSetCallingClass() {
        $query = new query_builder();
        $query->set_calling_class('callingclass');
        $this->assertEquals('callingclass',$query->get_calling_class());
    }
    
    public function testSetCallingViaConstructorClass() {
        $query = new query_builder('callingclass');
        $this->assertEquals('callingclass',$query->get_calling_class());
    }
    
    public function testGetNextTable() {
        $query = new query_builder();
        $letter = $query->get_table('testtable');
        $this->assertEquals($letter,$query->get_table('testtable'));
        $this->assertNotEquals($letter, $query->get_table('anothertable'));
    }
    
    /**
     * @dataProvider QueryProvider
     * @param unknown $class
     * @param unknown $query_callback
     * @param unknown $expect
     * @param unknown $except
     */
    public function testQuery($class,$query_callback,$expect,$except) {
        $query = new query_builder($class);
        try {
            $result = $query_callback($query);
        } catch (\Exception $e) {
            if (!$except) {
                $this->fail("Unexpected exception: ".$e->getMessage());
            } else {
                $this->assertTrue(true);
                return;
            }
        }
        if ($except) {
            $this->fail("Expected exception not raised.");
        }
        $this->assertEquals($expect,$result);
    }
    
    public function QueryProvider() {
        return [
            ['\Sunhill\ORM\Objects\oo_object', // Test simple count
                function($query) {
                return $query->count(true); 
                },'select count(a.id) as count from objects as a',false
            ],
            ['\Sunhill\ORM\Objects\oo_object', // Test simple get
                function($query) {
                    return $query->get(true);
                },'select a.id from objects as a',false
            ],
            ['\Sunhill\ORM\Objects\oo_object', // Test simple first
                function($query) {
                    return $query->first(true);
                },'select a.id from objects as a limit 0,1',false
            ],
            ['\Sunhill\ORM\Test\ts_dummy', // test simple where
                function($query) {
                    return $query->where('dummyint','=',1)->get(true);
                },"select a.id from dummies as a where a.dummyint = '1'",false
            ],
            ['\Sunhill\ORM\Test\ts_dummy', // test simple where
                function($query) {
                    return $query->where('dummyint','in',[1,2,3])->get(true);
                },"select a.id from dummies as a where a.dummyint in ('1','2','3')",false
            ],
            ['\Sunhill\ORM\Test\ts_dummy', // test simple where with default relation =
                function($query) {
                    return $query->where('dummyint',1)->get(true);
                },"select a.id from dummies as a where a.dummyint = '1'",false
            ],
            ['\Sunhill\ORM\Test\ts_testparent', // test and-combined where
                function($query) {
                    return $query->where('parentint','=',1)->where('parentchar','=','ABC')->get(true);
                },"select a.id from testparents as a where a.parentint = '1' and a.parentchar = 'ABC'",false
            ],
            ['\Sunhill\ORM\Test\ts_testchild', // test and-combined where in child
                function($query) {
                    return $query->where('childint','=',1)->where('childchar','=','ABC')->get(true);
                },"select a.id from testchildren as a where a.childint = '1' and a.childchar = 'ABC'",false
            ],
            ['\Sunhill\ORM\Test\ts_testchild', // test and-combined where of children with parent properties
                function($query) {
                    return $query->where('parentint','=',1)->where('parentchar','=','ABC')->get(true);
                },"select b.id from testparents as a inner join testchildren as b on b.id = a.id where a.parentint = '1' and a.parentchar = 'ABC'",false
            ],
            ['\Sunhill\ORM\Test\ts_testchild', // test and-combined where of children with mixed properties
                function($query) {
                    return $query->where('parentint','=',1)->where('childchar','=','ABC')->get(true);
                },"select b.id from testparents as a inner join testchildren as b on b.id = a.id where a.parentint = '1' and b.childchar = 'ABC'",false
            ],
                
        ];    
    }
}
