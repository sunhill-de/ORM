<?php

namespace Tests\Unit;

use Tests\TestCase;
use Sunhill\ORM\Search\query_builder;
use Sunhill\ORM\Search\query_atom;

class test_query_atom extends query_atom {
    
    protected $value;
    
    public function pub_set_singleton($value) {
        $this->is_singleton = $value;    
    }
    
    public function set_value($value) {
        $this->value = $value;
    }
    
    public function get_query_part() {
        if (isset($this->next)) {
            return $this->value.$this->connection.$this->next->get_query_part();
        }
        return $this->value;    
    }
}

class SearchAtomTest extends TestCase
{
    public function testLinking1() {
        $dummy = new query_builder();
        $test2 = new test_query_atom($dummy);
        $test2->pub_set_singleton(false);
        $test2->set_value('B');
        $test = new test_query_atom($dummy);
        $test->pub_set_singleton(false);
        $test->set_value('A');
        $test->link($test2,'+');
        $this->assertEquals('A+B',$test->get_query_part());
    }
    
    public function testLinking2() {
        $dummy = new query_builder();
        $test3 = new test_query_atom($dummy);
        $test3->pub_set_singleton(false);
        $test3->set_value('C');
        $test2 = new test_query_atom($dummy);
        $test2->pub_set_singleton(false);
        $test2->set_value('B');
        $test = new test_query_atom($dummy);
        $test->pub_set_singleton(false);
        $test->set_value('A');
        $test->link($test2,'+');
        $test->link($test3,'-');
        $this->assertEquals('A+B-C',$test->get_query_part());
    }
 
    public function testExceptionLinking() {
        $this->expectException(\Sunhill\ORM\Search\QueryException::class);
        $dummy = new query_builder();
        $test2 = new test_query_atom($dummy);
        $test2->pub_set_singleton(true);
        $test2->set_value('B');
        $test = new test_query_atom($dummy);
        $test->pub_set_singleton(true);
        $test->set_value('A');
        $test->link($test2,'+');
        
    }
}
