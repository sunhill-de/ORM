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
use Sunhill\ORM\Storage\Exceptions\ClassIsSameException;
use Sunhill\ORM\Storage\Exceptions\ClassNotARelativeException;

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
        $this->assertDatabaseHas('testchildren_childsarray',['id'=>17]);
        
        $test->dispatch('degrade',TestParent::class);
        
        $this->assertDatabaseMissing('testchildren',['id'=>17]);
        $this->assertDatabaseHas('objects',['id'=>17,'classname'=>'testparent']);        
        $this->assertDatabaseMissing('testchildren_childsarray',['id'=>17]);
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
        $this->assertDatabaseHas('thirdlevelchildren_thirdlevelsarray',['id'=>33]);
        $this->assertDatabaseHas('secondlevelchildren',['id'=>33]);
        
        $test->dispatch('degrade',ReferenceOnly::class);
        
        $this->assertDatabaseMissing('thirdlevelchildren',['id'=>33]);
        $this->assertDatabaseHas('objects',['id'=>33,'classname'=>'referenceonly']);
        $this->assertDatabaseMissing('thirdlevelchildren_thirdlevelsarray',['id'=>33]);
        $this->assertDatabaseMissing('secondlevelchildren',['id'=>33]);
    }
    
    public function testDegradeToSelfException()
    {
        $this->expectException(ClassIsSameException::class);
        
        $collection = new TestChild();
        $collection->load(17);

        $test = new MysqlStorage();
        $test->setCollection($collection);

        $test->dispatch('degrade',TestChild::class);        
    }
    
    
    public function testDegradeToNotAncestorException()
    {
        $this->expectException(ClassNotARelativeException::class);
        
        $collection = new TestChild();
        $collection->load(17);
        
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $test->dispatch('degrade',Dummy::class);        
    }
    
}