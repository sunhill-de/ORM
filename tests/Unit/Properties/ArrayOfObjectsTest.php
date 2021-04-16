<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\oo_property_arraybase;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Properties\oo_property_array_of_objects;

class ArrayOfObjectsTest extends TestCase
{
    public function testArrayEmpty() {
        $test = new oo_property_array_of_objects();
        $this->assertTrue($test->empty());
        return $test;
    }
    
    /**
     * @depends testArrayEmpty
     * @param unknown $test
     */
    public function testArrayNotEmpty($test) {        
        $test[] = new ts_dummy();
        $test[] = new ts_dummy();
        $test[] = new ts_dummy();
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
     * @return \Sunhill\ORM\Tests\Unit\Properties\oo_property_array_of_objects
     */
    public function testArrayCount() {
        $test = new oo_property_array_of_objects();
        $dummy1 = new ts_dummy(); $dummy1->dummyint = 11; $dummy1->set_ID(1);
        $dummy2 = new ts_dummy(); $dummy2->dummyint = 22; $dummy2->set_ID(2);
        $dummy3 = new ts_dummy(); $dummy3->dummyint = 33; $dummy3->set_ID(3);
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
        $this->assertEquals(22,$test[1]->dummyint);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayForeach($test) {
        $result = 'A';
        foreach ($test as $char) {
            $result .= $char->dummyint;
        }
        $this->assertEquals('A112233',$result);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_pass($test) {
        $dummy = new ts_dummy(); $dummy->set_ID(2);
        $this->assertTrue($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_fail($test) {
        $dummy = new ts_dummy(); $dummy->set_ID(992);
        $this->assertFalse($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayDirty($test) {
        $test->set_dirty(false);
        $dummy = new ts_dummy(); $dummy->set_ID(992);
        $test[] = $dummy;
        $this->assertTrue($test->get_dirty());
        return $test;
    }
    
    public function testNormalize_bothnormalized() {
        $test = new oo_property_array_of_objects();
        $test[] = 1;
        $test[] = 2;
        $dummy = new ts_dummy(); $dummy->set_ID(3);        
        $test[] = $dummy;
        $this->assertTrue($test->IsElementIn(1));
        $this->assertFalse($test->IsElementIn(999));
    }
    
    public function testNormalize_testnormalized() {
        $test = new oo_property_array_of_objects();
        $test[] = 1;
        $test[] = 2;
        $dummy = new ts_dummy(); $dummy->set_ID(3);
        $test[] = $dummy;
        $this->assertTrue($test->IsElementIn(3));
    }
    
    public function testNormalize_nonenormalized() {
        $test = new oo_property_array_of_objects();
        $test[] = 1;
        $test[] = 2;
        $dummy = new ts_dummy(); $dummy->set_ID(3);
        $test[] = $dummy;
        $this->assertTrue($test->IsElementIn($dummy));
    }
}