<?php

namespace Sunhill\ORM\Tests\Unit\Search;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Search\QueryBuilder;
use Sunhill\ORM\Search\QueryAtom;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\TestParent;
use Sunhill\ORM\Tests\Objects\TestChild;
use Sunhill\ORM\Facades\Classes;

class QueryBuilderTest extends TestCase
{
    public function setUp() : void {
        parent::setUp();
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
    }
    
    public function testSetCallingClass() {
        $query = new QueryBuilder();
        $query->setCallingClass('callingclass');
        $this->assertEquals('callingclass',$query->get_calling_class());
    }
    
    public function testSetCallingViaConstructorClass() {
        $query = new QueryBuilder('callingclass');
        $this->assertEquals('callingclass',$query->get_calling_class());
    }
    
    public function testGetNextTable() {
        $query = new QueryBuilder();
        $letter = $query->getTable('testtable');
        $this->assertEquals($letter,$query->getTable('testtable'));
        $this->assertNotEquals($letter, $query->getTable('anothertable'));
    }
    
    /**
     * @dataProvider QueryProvider
     * @param unknown $class
     * @param unknown $query_callback
     * @param unknown $expect
     * @param unknown $except
     */
    public function testQuery($class,$query_callback,$expect,$except) {
        $query = new QueryBuilder($class);
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
            ['\Sunhill\ORM\Objects\ORMObject', // Test simple count
                function($query) {
                return $query->count(true); 
                },'select count(a.id) as count from objects as a',false
            ],
            ['\Sunhill\ORM\Objects\ORMObject', // Test simple get
                function($query) {
                    return $query->get(true);
                },'select a.id from objects as a',false
            ],
            ['\Sunhill\ORM\Objects\ORMObject', // Test simple first
                function($query) {
                    return $query->first(true);
                },'select a.id from objects as a limit 0,1',false
            ],
            ['\Sunhill\ORM\Tests\Objects\Dummy', // test simple where
                function($query) {
                    return $query->where('dummyint','=',1)->get(true);
                },"select a.id from dummies as a where a.dummyint = '1'",false
            ],
            ['\Sunhill\ORM\Tests\Objects\Dummy', // test simple where
                function($query) {
                    return $query->where('dummyint','in',[1,2,3])->get(true);
                },"select a.id from dummies as a where a.dummyint in ('1','2','3')",false
            ],
            ['\Sunhill\ORM\Tests\Objects\Dummy', // test simple where with default relation =
                function($query) {
                    return $query->where('dummyint',1)->get(true);
                },"select a.id from dummies as a where a.dummyint = '1'",false
            ],
            ['\Sunhill\ORM\Tests\Objects\TestParent', // test and-combined where
                function($query) {
                    return $query->where('parentint','=',1)->where('parentchar','=','ABC')->get(true);
                },"select a.id from testparents as a where a.parentint = '1' and a.parentchar = 'ABC'",false
            ],
            ['\Sunhill\ORM\Tests\Objects\TestChild', // test and-combined where in child
                function($query) {
                    return $query->where('childint','=',1)->where('childchar','=','ABC')->get(true);
                },"select a.id from testchildren as a where a.childint = '1' and a.childchar = 'ABC'",false
            ],
            ['\Sunhill\ORM\Tests\Objects\TestChild', // test and-combined where of children with parent properties
                function($query) {
                    return $query->where('parentint','=',1)->where('parentchar','=','ABC')->get(true);
                },"select b.id from testparents as a inner join testchildren as b on b.id = a.id where a.parentint = '1' and a.parentchar = 'ABC'",false
            ],
            ['\Sunhill\ORM\Tests\Objects\TestChild', // test and-combined where of children with mixed properties
                function($query) {
                    return $query->where('parentint','=',1)->where('childchar','=','ABC')->get(true);
                },"select b.id from testparents as a inner join testchildren as b on b.id = a.id where a.parentint = '1' and b.childchar = 'ABC'",false
            ],
                
        ];    
    }
}
