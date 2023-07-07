<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;

class DegradeTest extends DatabaseTestCase
{
    
    /**
     * @group object
     */
    public function testTestChild()
    {
        $collection = new TestChild();
        $collection->load(17);
        
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseHas('testchildren',['id'=>17]);
        $this->assertDatabaseHas('objects',['id'=>17,'classname'=>'testchild']);
        $this->assertDatabaseHasTable('testchildren_childsarray');
        
        $test->dispatch('degrade',TestParent::class);
        
        $this->assertDatabaseMissing('testchildren',['id'=>17]);
        $this->assertDatabaseHas('objects',['id'=>17,'classname'=>'testparent']);        
        $this->assertDatabaseMissingTable('testchildren_childsarray');
    }
    
    /**
     * @group object
     */
    public function testThirdLevelChild()
    {
        $collection = new ThirdLevelChild();
        $collection->load(33);
        
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $this->assertDatabaseHas('thirdlevelchildren',['id'=>33]);
        $this->assertDatabaseHas('objects',['id'=>33,'classname'=>'thirdlevelchild']);
        $this->assertDatabaseHasTable('thirdlevelchildren_thirdlevelsarray');
        $this->assertDatabaseHas('secondlevelchildren',['id'=>33]);
        
        $test->dispatch('degrade',ReferenceOnly::class);
        
        $this->assertDatabaseMissing('thirdlevelchildren',['id'=>33]);
        $this->assertDatabaseHas('objects',['id'=>33,'classname'=>'referenceonly']);
        $this->assertDatabaseMissingTable('thirdlevelchildren_thirdlevelsarray');
        $this->assertDatabaseMissing('secondlevelchildren',['id'=>33]);
    }
    
}