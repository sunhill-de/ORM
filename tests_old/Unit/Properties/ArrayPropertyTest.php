<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\PropertyArrayBase;

class TestArray1 extends PropertyArrayBase {

    protected $type = 'test_array';
    
    protected $features = ['array'];
    
}

class TestArray2 extends PropertyArrayBase {

    protected $type = 'test_array';
    
    protected $features = ['array'];
    
    protected $int_values = ['A'=>1,'B'=>2,'C'=>3];
    
    protected function NormalizeValue($value) {
        if (is_string($value)) {
            return $this->int_values[$value];
        } else if (is_int($value)) {
            return $value;
        } else {
            throw \Exception("Unknown type");
        }
    }
}

class ArrayPropertyTest extends TestCase
{
    public function testArrayEmpty() {
        $test = new TestArray1();
        $this->assertTrue($test->empty());
        return $test;
    }
    
    /**
     * @depends testArrayEmpty
     * @param unknown $test
     */
    public function testArrayNotEmpty($test) {
        $test[] = 'A';
        $test[] = 'B';
        $test[] = 'C';
        $this->assertFalse($test->empty());
        return $test;
    }
    
    /**
     * @depends testArrayNotEmpty
     * @param unknown $test
     */
    public function testArrayClear($test) {
        $test->clear();
        $this->assertTrue($test->empty());
        return $test;
    }
    
    /**
     * @return \Sunhill\ORM\Tests\Unit\Properties\TestArray1
     */
    public function testArrayCount() {
        $test = new TestArray1();
        $test[] = 'A';
        $test[] = 'B';
        $test[] = 'C';
        $this->assertEquals(3,count($test));
        return $test;
    }

    /**
     * @depends testArrayCount
     */
    public function testArrayIndex($test) {
        $this->assertEquals('B',$test[1]);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayForeach($test) {
        $result = '';
        foreach ($test as $char) {
            $result .= $char;
        }
        $this->assertEquals('ABC',$result);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_pass($test) {
        $this->assertTrue($test->IsElementIn('A'));
        return $test;
    }

    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_fail($test) {
        $this->assertFalse($test->IsElementIn('Z'));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayDirty($test) {
        $test->setDirty(false);
        $test[] = 'D';
        $this->assertTrue($test->getDirty());
        return $test;
    }
    
    public function testNormalize_bothnormalized() {
        $test = new TestArray2();
        $test[] = 1;
        $test[] = 2;
        $test[] = 'C';
        $this->assertTrue($test->IsElementIn(1));
        $this->assertFalse($test->IsElementIn(999));
    }
    
    public function testNormalize_testnormalized() {
        $test = new TestArray2();
        $test[] = 1;
        $test[] = 2;
        $test[] = 'C';
        $this->assertTrue($test->IsElementIn(3));
    }
    
    public function testNormalize_intnormalized() {
        $test = new TestArray2();
        $test[] = 1;
        $test[] = 2;
        $test[] = 'C';
        $this->assertTrue($test->IsElementIn('B'));
    }
    
    public function testNormalize_nonenormalized() {
        $test = new TestArray2();
        $test[] = 1;
        $test[] = 2;
        $test[] = 'C';
        $this->assertTrue($test->IsElementIn('C'));
    }
}