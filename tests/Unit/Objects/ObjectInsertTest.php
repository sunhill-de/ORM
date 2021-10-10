<?php
/**
 *
 * @file ObjectInsertTest.php
 * Unittest for insertion of objects
 * Lang en
 * Reviewstate: 2020-08-12
 */

namespace Sunhill\ORM\Tests\Unit\Objects;

use Illuminate\Foundation\Testing\WithFaker;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Objects\ts_objectunit;
use Sunhill\ORM\Facades\Objects;

class ObjectInsertTest extends DBTestCase
{
   
    public function testStorageCreation() {
        $object = new ts_objectunit();
        $object->intvalue = 666;
        $object->sarray[] = 'AAA';
        $object->sarray[] = 'BBB';
        $object->sarray[] = 'CCC';
        $dummy1 = Objects::load(1);
        $dummy2 = Objects::load(2);
        $dummy3 = Objects::load(3);
        $dummy4 = Objects::load(4);
        $object->objectvalue = $dummy1;
        $object->oarray[] = $dummy2;
        $object->oarray[] = $dummy3;
        $object->oarray[] = $dummy4;
        $object->tags->stick(1);
        $object->tags->stick(2);
        $object->general_attribute = 321;
        $object->commit();
        $this->assertEquals(1,$object->get_id());
        return $object;
    }
    
    /**
     * @depends testStorageCreation
     * @param unknown $object
     */
    public function testSimpleField($object) {
        $this->assertEquals(666,$object->storage_values['intvalue']);
        return $object;
    }
    
    /**
     * @depends testStorageCreation
     * @param unknown $object
     */
    public function testObjectField($object) {
        $this->assertEquals(1,$object->storage_values['objectvalue']);
        return $object;
    }
    
    /**
     * @depends testStorageCreation
     * @param unknown $object
     */
    public function testOArrayField($object) {
        $this->assertEquals(3,$object->storage_values['oarray'][1]);
        return $object;
    }
    
    /**
     * @depends testStorageCreation
     * @param unknown $object
     */
    public function testSArrayField($object) {
        $this->assertEquals('BBB',$object->storage_values['sarray'][1]);
        return $object;
    }
    
    
    /**
     * @depends testStorageCreation
     * @param unknown $object
     */
    public function testTagField($object) {
        $this->assertEquals(2,$object->storage_values['tags'][1]);
        return $object;
    }
    
    /**
     * @depends testStorageCreation
     * @param unknown $object
     */
    public function testAttributeField($object) {
        $this->assertEquals(321,$object->storage_values['attributes']['general_attribute']['value']);
        return $object;
    }
    
    public function testSimpleOnly() {
        $object = new ts_objectunit();
        $object->intvalue = 666;
        $object->commit();
        $this->assertEquals(666,$object->storage_values['intvalue']);
    }
}
