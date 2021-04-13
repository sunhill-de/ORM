<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\oo_property_arraybase;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Properties\oo_property_array_of_objects;

class ArrayOfObjectsTest extends TestCase
{
    public function testArrayCount() {
        $test = new oo_property_array_of_objects();
        $obj1 = new ts_dummy();
        $obj1->set_id(1);
        $obj2 = new ts_dummy();
        $obj2->set_id(2);
        $test[] = $obj1;
        $test[] = $obj2;
        $test[] = 3;
        $this->assertEquals(3,count($test));
        return $test;
    }

    /**
     * @depends testArrayCount
     */
    public function testArrayIndex($test) {
        $this->assertEquals(2,$test[1]->get_id());
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayForeach($test) {
        $result = 'A';
        foreach ($test as $char) {
            $result .= (is_int($char)?$char:$char->get_id());
        }
        $this->assertEquals('A123',$result);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_pass($test) {
        $this->assertTrue($test->IsElementIn(1));
        return $test;
    }

    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_fail($test) {
        $this->assertFalse($test->IsElementIn(999));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayDirty($test) {
        $test->set_dirty(false);
        $test[] = 'D';
        $this->assertTrue($test->get_dirty());
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