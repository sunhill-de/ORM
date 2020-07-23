<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Sunhill\Test\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class ObjectInsertTest extends sunhill_testcase_db
{

    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_special_table('passthrus');
        $this->create_special_table('testparents');
        $this->create_special_table('testchildren');
        $this->create_special_table('referenceonlies');
    }
    
    protected function prepare_read() {
        $this->prepare_tables();
        $this->create_load_scenario();
    }
    
    public function testStorageCreation() {
        $this->prepare_read();
        $object = new \Sunhill\Test\ts_objectunit();
        $object->intvalue = 666;
        $object->sarray[] = 'AAA';
        $object->sarray[] = 'BBB';
        $object->sarray[] = 'CCC';
        $dummy1 = \Sunhill\Objects\oo_object::load_object_of(1);
        $dummy2 = \Sunhill\Objects\oo_object::load_object_of(2);
        $dummy3 = \Sunhill\Objects\oo_object::load_object_of(3);
        $dummy4 = \Sunhill\Objects\oo_object::load_object_of(4);
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
        $this->prepare_read();
        $object = new \Sunhill\Test\ts_objectunit();
        $object->intvalue = 666;
        $object->commit();
        $this->assertEquals(666,$object->storage_values['intvalue']);
    }
}
