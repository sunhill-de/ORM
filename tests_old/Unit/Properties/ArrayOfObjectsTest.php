<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\PropertyArrayBase;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Properties\PropertyArrayOfObjects;

class ArrayOfObjectsTest extends TestCase
{
    public function testArrayEmpty() {
        $test = new PropertyArrayOfObjects();
        $this->assertTrue($test->empty());
        return $test;
    }
    
    /**
     * @depends testArrayEmpty
     * @param unknown $test
     */
    public function testArrayNotEmpty($test) {        
        $test[] = new Dummy();
        $test[] = new Dummy();
        $test[] = new Dummy();
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
     * @return \Sunhill\ORM\Tests\Unit\Properties\PropertyArrayOfObjects
     */
    public function testArrayCount() {
        $test = new PropertyArrayOfObjects();
        $dummy1 = new Dummy(); $dummy1->dummyint = 11; $dummy1->setID(1);
        $dummy2 = new Dummy(); $dummy2->dummyint = 22; $dummy2->setID(2);
        $dummy3 = new Dummy(); $dummy3->dummyint = 33; $dummy3->setID(3);
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
        $dummy = new Dummy(); $dummy->setID(2);
        $this->assertTrue($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_fail($test) {
        $dummy = new Dummy(); $dummy->setID(992);
        $this->assertFalse($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayDirty($test) {
        $test->setDirty(false);
        $dummy = new Dummy(); $dummy->setID(992);
        $test[] = $dummy;
        $this->assertTrue($test->getDirty());
        return $test;
    }
    
    public function testNormalize_bothnormalized() {
        $test = new PropertyArrayOfObjects();
        $test[] = 1;
        $test[] = 2;
        $dummy = new Dummy(); $dummy->setID(3);        
        $test[] = $dummy;
        $this->assertTrue($test->IsElementIn(1));
        $this->assertFalse($test->IsElementIn(999));
    }
    
    public function testNormalize_testnormalized() {
        $test = new PropertyArrayOfObjects();
        $test[] = 1;
        $test[] = 2;
        $dummy = new Dummy(); $dummy->setID(3);
        $test[] = $dummy;
        $this->assertTrue($test->IsElementIn(3));
    }
    
    public function testNormalize_nonenormalized() {
        $test = new PropertyArrayOfObjects();
        $test[] = 1;
        $test[] = 2;
        $dummy = new Dummy(); $dummy->setID(3);
        $test[] = $dummy;
        $this->assertTrue($test->IsElementIn($dummy));
    }
}