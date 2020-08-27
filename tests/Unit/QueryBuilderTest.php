<?php

namespace Tests\Unit;

use Tests\TestCase;
use Sunhill\Search\query_builder;
use Sunhill\Search\query_atom;

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
    
    public function testSimpleCountQuery() {
        $query = new query_builder('\Sunhill\Objects\oo_object');
        $result = $query->count(true);
        $this->assertEquals('select count(a.id) from objects as a',$result);
    }
    
    public function testSimpleIDQuery() {
        $query = new query_builder('\Sunhill\Objects\oo_object');
        $result = $query->get(true);
        $this->assertEquals('select a.id from objects as a',$result);
    }

    public function testSimpleFirstQuery() {
        $query = new query_builder('\Sunhill\Objects\oo_object');
        $result = $query->first(true);
        $this->assertEquals('select a.id from objects as a limit 0,1',$result);
    }
    
    public function testSimpleWhere() {
        $query = new query_builder('\Sunhill\Test\ts_dummy');
        $result = $query->where('dummyint','=',1)->get(true);
        $this->assertEquals("select a.id from dummies as a where a.dummyint = '1'",$result);
    }
    
    public function testCombinedWhere() {
        $query = new query_builder('\Sunhill\Test\ts_testparent');
        $result = $query->where('parentint','=',1)->where('parentchar','=','ABC')->get(true);
        $this->assertEquals("select a.id from testparents as a where a.parentint = '1' and a.parentchar = 'ABC'",$result);
    }
}
