<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class DeleteTest extends DatabaseTestCase
{
    
    /**
     * @group deleteobject
     * @group object
     * @group delete
     */
    public function testDummy()
    {
        $object = new Dummy();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->assertDatabaseHas('objects', ['id'=>1]);
        $this->assertDatabaseHas('dummies', ['id'=>1]);
        $this->assertDatabaseHas('tagobjectassigns', ['container_id'=>1]);
        $this->assertDatabaseHas('attributeobjectassigns', ['object_id'=>1]);
        $this->assertDatabaseHas('attr_general_attribute', ['object_id'=>1]);
        
        $test->dispatch('delete', 1);
        
        $this->assertDatabaseMissing('objects', ['id'=>1]);
        $this->assertDatabaseMissing('dummies', ['id'=>1]);
        $this->assertDatabaseMissing('tagobjectassigns', ['container_id'=>1]);
        $this->assertDatabaseMissing('attributeobjectassigns', ['object_id'=>1]);
        $this->assertDatabaseMissing('attr_general_attribute', ['object_id'=>1]);
    }

    /**
     * @group deleteobject
     * @group object
     * @group delete
     */
    public function testDummyChildLittleMoreComplexDelete()
    {
        $object = new DummyChild();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $test->dispatch('delete', 8);
        
        $this->assertDatabaseMissing('dummychildren', ['id'=>8]);
        $this->assertDatabaseMissing('dummies', ['id'=>8]);
        $this->assertDatabaseMissing('objects', ['id'=>8]);
    }
    
    /**
     * @group deleteobject
     * @group object
     * @group delete
     */
    public function testTestParent()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        $test->setCollection($object);
        
        $test->dispatch('delete',9);
        
        $this->assertDatabaseMissing('testparents',['id'=>9]);
        $this->assertDatabaseMissing('testparents_parentoarray', ['id'=>9]);
        $this->assertDatabaseMissing('testparents_parentsarray', ['id'=>9]);
    }
    
    /**
     * @group deleteobject
     * @group object
     * @group delete
     */
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage($object);
        $test->setCollection($object);
        
        $test->dispatch('delete',18);

        $this->assertDatabaseMissing('testparents',['id'=>18]);
        $this->assertDatabaseMissing('testparents_parentoarray', ['id'=>18]);
        $this->assertDatabaseMissing('testparents_parentsarray', ['id'=>18]);
        
        $this->assertDatabaseMissing('testchildren',['id'=>18]);
        $this->assertDatabaseMissing('testchildren_childoarray', ['id'=>18]);
        $this->assertDatabaseMissing('testchildren_childsarray', ['id'=>18]);        
    }
}