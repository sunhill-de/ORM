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
    
    
}
