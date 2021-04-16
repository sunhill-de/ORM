<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_tag;
use Sunhill\ORM\Properties\oo_property_tags;

class TagsTest extends DBTestCase
{
    public function testArrayEmpty() {
        $test = new oo_property_tags();
        $this->assertTrue($test->empty());
        return $test;
    }
    
    /**
     * @depends testArrayEmpty
     * @param unknown $test
     */
    public function testArrayNotEmpty($test) {        
        $test[] = new oo_tag();
        $test[] = new oo_tag();
        $test[] = new oo_tag();
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
     * @return \Sunhill\ORM\Tests\Unit\Properties\oo_property_tags
     */
    public function testArrayCount() {
        $test = new oo_property_tags();
        $dummy1 = new oo_tag(); $dummy1->set_name('TagA');
        $dummy2 = new oo_tag(); $dummy2->set_name('TagB');
        $dummy3 = new oo_tag(); $dummy3->set_name('TagC');
        $test[] = $dummy1;
        $test[] = $dummy2;
        $test[] = $dummy3;
        $this->assertEquals(3,count($test));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayIndex($test) {
        $this->assertEquals('TagB',$test[1]);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayForeach($test) {
        $result = '';
        foreach ($test as $char) {
            $result .= $char->get_name();
        }
        $this->assertEquals('TagATagBTagC',$result);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_pass($test) {
        $dummy = new oo_tag(); $dummy->set_name('TagA');
        $this->assertTrue($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_fail($test) {
        $dummy = new oo_tag(); $dummy->set_name('TagZ');
        $this->assertFalse($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayDirty($test) {
        $test->set_dirty(false);
        $dummy = new oo_tag(); $dummy->set_name('TagD');
        $test[] = $dummy;
        $this->assertTrue($test->get_dirty());
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testNormalize_testIsString($test) {
        $this->assertTrue($test->IsElementIn('TagA'));
        $this->assertFalse($test->IsElementIn('TagZ'));
    }
    
    /**
     * @depends testArrayCount
     */
    public function testNormalize_testIsTag($test) {
        $dummy1 = new oo_tag();
        $dummy1->set_name('TagA');
        $dummy2 = new oo_tag();
        $dummy2->set_name('TagZ');
        $this->assertTrue($test->IsElementIn($dummy1));
        $this->assertFalse($test->IsElementIn($dummy2));
    }
    
    /**
     * @depends testArrayCount
     */
    public function testNormalize_testIsInt($test) {
        $this->assertTrue($test->IsElementIn(1));
        $this->assertFalse($test->IsElementIn(6));
    }
}