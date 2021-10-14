<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Properties\PropertyTags;

class TagsTest extends DBTestCase
{
    public function testArrayEmpty() {
        $test = new PropertyTags();
        $this->assertTrue($test->empty());
        return $test;
    }
    
    /**
     * @depends testArrayEmpty
     * @param unknown $test
     */
    public function testArrayNotEmpty($test) {        
        $test[] = new Tag();
        $test[] = new Tag();
        $test[] = new Tag();
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
     * @return \Sunhill\ORM\Tests\Unit\Properties\PropertyTags
     */
    public function testArrayCount() {
        $test = new PropertyTags();
        $dummy1 = new Tag(); $dummy1->setName('TagA');
        $dummy2 = new Tag(); $dummy2->setName('TagB');
        $dummy3 = new Tag(); $dummy3->setName('TagC');
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
            $result .= $char->getName();
        }
        $this->assertEquals('TagATagBTagC',$result);
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_pass($test) {
        $dummy = new Tag(); $dummy->setName('TagA');
        $this->assertTrue($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayHasValue_fail($test) {
        $dummy = new Tag(); $dummy->setName('TagZ');
        $this->assertFalse($test->IsElementIn($dummy));
        return $test;
    }
    
    /**
     * @depends testArrayCount
     */
    public function testArrayDirty($test) {
        $test->setDirty(false);
        $dummy = new Tag(); $dummy->setName('TagD');
        $test[] = $dummy;
        $this->assertTrue($test->getDirty());
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
        $dummy1 = new Tag();
        $dummy1->setName('TagA');
        $dummy2 = new Tag();
        $dummy2->setName('TagZ');
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