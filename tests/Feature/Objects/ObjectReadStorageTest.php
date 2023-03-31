<?php

namespace Sunhill\ORM\Tests\Feature\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;

class ObjectReadStorageTest extends DatabaseTestCase
{

    /**
     * @group load
     * @return \Sunhill\ORM\Test\ObjectUnit
     */
    public function testStorageCreation() {
        $object = new ObjectUnit();
        $object->storage_values = [
            'id'=>1,
            'created_at'=>'2019-10-06 12:05:00',
            'modified_at'=>'2019-10-06 12:05:00',
            'intvalue'=>123,
            'objectvalue'=>2,
            'sarray'=>['ABC','DEF','GHI'],
            'oarray'=>[3,4],
            'calcvalue'=>'123A',
            'tags'=>[1,2,3,4],
            'attributes'=>['general_attribute'=>[
                'attribute_id'=>4,
                'value_id'=>1,
                'object_id'=>1,
                'value'=>12,
                'textvalue'=>'',
                'name'=>'general_attribute',
                'allowedobjects'=>"\\Sunhill\\Objects\\ORMObject",
                'type'=>'int',
                'property'=>''                
            ]]
        ];
        $object->publicLoad(1);
        $this->assertEquals(1,$object->getID());
        return $object;
    }
    
    /**
     * @group load
     * @depends testStorageCreation
     */
    public function testSimpleValue($object) {
        $this->assertEquals($object->storage_values['intvalue'],$object->intvalue);
        return $object;
    }
    
    /**
     * @group load
     * @depends testStorageCreation
     */
    public function testObjectValue($object) {
        $this->assertEquals(234,$object->objectvalue->dummyint);
        return $object;
    }
    
    /**
     * @group load
     * @depends testStorageCreation
     */
    public function testSArrayValue($object) {
        $this->assertEquals('DEF',$object->sarray[1]);
        return $object;
    }
    
    /**
     * @group load
     * @depends testStorageCreation
     */
    public function testOArrayValue($object) {
        $this->assertEquals(456,$object->oarray[1]->dummyint);
        return $object;
    }
    
    /**
     * @group load
     * @depends testStorageCreation
     */
    public function testTagValue($object) {
        $this->assertEquals('TagB',$object->tags[1]);
        return $object;
    }
    
    /**
     * @group load
     * @depends testStorageCreation
     */
    public function testAttributeValue($object) {
        $this->assertEquals(12,$object->general_attribute);
        return $object;
    }
    
    /**
     * @group load
     * @depends testStorageCreation
     */
    public function testCalcValue($object) {
        $this->assertEquals('123A',$object->calcvalue);
        return $object;
    }
        
}
