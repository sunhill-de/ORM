<?php

namespace Tests\Unit;

use Tests\TestCase;
use Sunhill\Traits\TraversableArray;

class example_traversablearray1 implements \ArrayAccess,\Countable,\Iterator {
    
    use TraversableArray;
        
}

class example_traversablearray2 implements \ArrayAccess,\Countable,\Iterator {
    
    use TraversableArray;

    protected $other_fields = [];
    
    public $flag = '';
    
     protected function get_fields() {
         return $this->otherfields;
     }
     
     protected function element_change($offset,$value) {
         $this->otherfields[$offset] = $value;
     }
     
     protected function element_append($value) {
         $this->otherfields[] = $value;
     }
     
     protected function element_unset($offset) {
         unset($this->otherfields[$offset]);
     }
     
     protected function element_changing($descriptor) {
        $this->flag = 'changing:'.$descriptor->from."=>".$descriptor->to;        
     }
     
     protected function element_changed($descriptor) {
         $this->flag = 'changed:'.$descriptor->from."=>".$descriptor->to;         
     }
     
}

class TraitTraversableArrayTest extends TestCase
{

    public function testSimpleTraversableArray_count() {
        $test = new example_traversablearray1();
        $test[] = 'A';
        $test[] = 'B';
        $this->assertEquals(2,count($test));
        return $test;
    }
    
    /**
     * @depends testSimpleTraversableArray_count
     * @param unknown $test
     */
    public function testSimpleTraversableArray_index($test) {
        $this->assertEquals('A',$test[0]);
        return $test;
    }
    
    /**
     * @depends testSimpleTraversableArray_count
     * @param unknown $test
     */
    public function testSimpleTraversableArray_elementset($test) {
        $test[1] = 'C';
        $this->assertEquals('C',$test[1]);
        return $test;
    }
    
    /**
     * @depends testSimpleTraversableArray_count
     * @param unknown $test
     */
    public function testSimpleTraversableArray_unset($test) {
        unset($test[0]);
        $this->assertEquals(1,count($test));
        return $test;
    }
    
    
    /**
     * @depends testSimpleTraversableArray_count
     * @param unknown $test
     */
    public function testSimpleTraversableArray_foreach($test) {
        $result = '';
        foreach ($test as $key) {
            $result .= $key;
        }
        $this->assertEquals('AB',$result);
        return $test;
    }
    
    public function testComplexTraversableArray_count() {
        $test = new example_traversablearray2();
        $test[] = 'A';
        $test[] = 'B';
        $this->assertEquals(2,count($test));
        return $test;
    }
    
    /**
     * @depends testComplexTraversableArray_count
     * @param unknown $test
     */
    public function testComplexTraversableArray_index($test) {
        $this->assertEquals('A',$test[0]);
        return $test;
    }
    
    /**
     * @depends testComplexTraversableArray_count
     * @param unknown $test
     */
    public function testComplexTraversableArray_elementset($test) {
        $test[1] = 'C';
        $this->assertEquals('C',$test[1]);
        return $test;
    }
    
    /**
     * @depends testComplexTraversableArray_count
     * @param unknown $test
     */
    public function testComplexTraversableArray_unset($test) {
        unset($test[0]);
        $this->assertEquals(1,count($test));
        return $test;
    }
    
    
    /**
     * @depends testComplexTraversableArray_count
     * @param unknown $test
     */
    public function testComplexTraversableArray_foreach($test) {
        $result = '';
        foreach ($test as $key) {
            $result .= $key;
        }
        $this->assertEquals('AB',$result);
        return $test;
    }
    
}
