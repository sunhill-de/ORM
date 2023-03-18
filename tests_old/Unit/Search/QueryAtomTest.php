<?php

namespace Sunhill\ORM\Tests\Unit\Search;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Search\QueryBuilder;
use Sunhill\ORM\Search\QueryAtom;

class test_QueryAtom extends QueryAtom {
    
    protected $value;
    
    public function pub_set_singleton($value) {
        $this->is_singleton = $value;    
    }
    
    public function setValue($value) {
        $this->value = $value;
    }
    
    public function getQueryPart() {
        if (isset($this->next)) {
            return $this->value.$this->connection.$this->next->getQueryPart();
        }
        return $this->value;    
    }
}

class QueryAtomTest extends TestCase
{
    public function testLinking1() {
        $dummy = new QueryBuilder();
        $test2 = new test_QueryAtom($dummy);
        $test2->pub_set_singleton(false);
        $test2->setValue('B');
        $test = new test_QueryAtom($dummy);
        $test->pub_set_singleton(false);
        $test->setValue('A');
        $test->link($test2,'+');
        $this->assertEquals('A+B',$test->getQueryPart());
    }
    
    public function testLinking2() {
        $dummy = new QueryBuilder();
        $test3 = new test_QueryAtom($dummy);
        $test3->pub_set_singleton(false);
        $test3->setValue('C');
        $test2 = new test_QueryAtom($dummy);
        $test2->pub_set_singleton(false);
        $test2->setValue('B');
        $test = new test_QueryAtom($dummy);
        $test->pub_set_singleton(false);
        $test->setValue('A');
        $test->link($test2,'+');
        $test->link($test3,'-');
        $this->assertEquals('A+B-C',$test->getQueryPart());
    }
 
    public function testExceptionLinking() {
        $this->expectException(\Sunhill\ORM\Search\QueryException::class);
        $dummy = new QueryBuilder();
        $test2 = new test_QueryAtom($dummy);
        $test2->pub_set_singleton(true);
        $test2->setValue('B');
        $test = new test_QueryAtom($dummy);
        $test->pub_set_singleton(true);
        $test->setValue('A');
        $test->link($test2,'+');
        
    }
}
