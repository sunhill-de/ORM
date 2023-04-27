<?php

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Storage\FileStorage;
use Sunhill\ORM\Objects\ORMObject;

class StorageBaseTest extends DatabaseTestCase
{
    
    public function testGetCaller()
    {
        $object = new ORMObject();
        $test = new FileStorage($object);
        
        $this->assertEquals($object, $test->getCaller());
    }
    
    public function testEntities1()
    {
        $object = new ORMObject();
        $test = new FileStorage($object);
        
        $this->assertTrue(is_null($test->getEntity('test')));
        $test->setEntity('test','TESTVALUE');
        $this->assertEquals('TESTVALUE', $test->getEntity('test'));
    }
    
    public function testEntities2()
    {
        $object = new ORMObject();
        $test = new FileStorage($object);
        
        $this->assertTrue(is_null($test->test));
        $test->test = 'TESTVALUE';
        $this->assertEquals('TESTVALUE', $test->test);
    }
    
}