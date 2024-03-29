<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class DropTest extends DatabaseTestCase
{
    
    /**
     * @group dropobject
     * @group object
     * @group drop
     */
    public function testDummyChildDelete()
    {
        $object = new DummyChild();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $test->dispatch('drop');
        
        $this->assertDatabaseHasNotTable('dummychildren');
        $this->assertDatabaseHasTable('dummies');
        $this->assertDatabaseMissing('objects', ['class'=>'dummychild']);
    }
    
    /**
     * @group dropobject
     * @group object
     * @group drop
     */
    public function testDummy()
    {
        $object = new Dummy();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $test->dispatch('drop');
        
        $this->assertDatabaseMissing('tagobjectassigns', ['container_id'=>1]);
        $this->assertDatabaseMissing('attributeobjectassigns', ['object_id'=>1]);
        $this->assertDatabaseMissing('attr_general_attribute', ['object_id'=>1]);
        $this->assertDatabaseMissing('objects', ['classname'=>'dummy']);
        $this->assertDatabaseMissing('objects', ['classname'=>'dummychild']);
        $this->assertDatabaseHasNotTable('dummies');
    }

    /**
     * @group dropobject
     * @group object
     * @group drop
     */
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage();
        $test->setCollection($object);
                
        $test->dispatch('drop');
        
        $this->assertDatabaseHasNotTable('testchildren');        
        $this->assertDatabaseMissing('testparents',['id'=>18]);
        $this->assertDatabaseHasNotTable('testchildren_array_childsarray');
        $this->assertDatabaseHasNotTable('testchildren_array_childoarray');
        $this->assertDatabaseHasNotTable('testchildren_calc_childcalc');        
    }

    /**
     * @group dropobject
     * @group object
     * @group drop
     */
    public function testTestParent()
    {
        $object = new TestParent();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $test->dispatch('drop');
        
        
        $this->assertDatabaseHasNotTable('testchildren');
        $this->assertDatabaseHasNotTable('testparents');

        $this->assertDatabaseHasNotTable('testchildren_array_childsarray');
        $this->assertDatabaseHasNotTable('testchildren_array_childoarray');
        $this->assertDatabaseHasNotTable('testchildren_calc_childcalc');
        
        $this->assertDatabaseHasNotTable('testparents_array_parentsarray');
        $this->assertDatabaseHasNotTable('testparents_array_parentoarray');
        $this->assertDatabaseHasNotTable('testparents_calc_parentcalc');
        
    }
    
}