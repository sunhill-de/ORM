<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\oo_property_array_of_strings;

class ArrayOfStringsTest extends TestCase
{
    public function testArrayCount() {
        $test = new oo_property_array_of_strings();
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
        $test->set_dirty(false);
        $test[] = 'D';
        $this->assertTrue($test->get_dirty());
        return $test;
    }
    
}