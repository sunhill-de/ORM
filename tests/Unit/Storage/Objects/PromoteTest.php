<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Storage\Exceptions\ClassNotARelativeException;
use Sunhill\ORM\Storage\Exceptions\ClassIsSameException;

class PromoteTest extends DatabaseTestCase
{
    
    public function testUpgradeDummyToDummychild()
    {
        $collection = new Dummy();
        $collection->load(1);
        
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $test->dispatch('promote',DummyChild::class, ['dummychildint'=>543]);
        
        $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
        $this->assertDatabaseHas('dummychildren',['id'=>1,'dummychildint'=>543]);
        $this->assertDatabaseHas('objects',['id'=>1,'classname'=>'dummychild']);        
    }
   
    public function testUpgradeTestParentToTestChild()
    {
        $collection = new TestParent();
        $collection->load(9);
        
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $test->dispatch('promote',TestChild::class, 
            [
                'childint'=>543,
                'childchar'=>'ADA',
                'childfloat'=>5.34,
                'childtext'=>'Like a rolling stone',
                'childdatetime'=>'2023-10-10 12:34:55',
                'childtime'=>'12:34:55',
                'childdate'=>'2023-10-10',
                'childenum'=>'testA'
            ]);
        
        $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
        $this->assertDatabaseHas('dummychildren',['id'=>1,'dummychildint'=>543]);
        $this->assertDatabaseHas('objects',['id'=>1,'classname'=>'dummychild']);
    }
    
    public function testDegradeToSelfException()
    {
        $this->expectException(ClassIsSameException::class);
        
        $collection = new Dummy();
        $collection->load(1);
        
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $test->dispatch('promote',Dummy::class,[]);
    }
    
    
    public function testDegradeToNotDescendantException()
    {
        $this->expectException(ClassNotARelativeException::class);
        
        $collection = new Dummy();
        $collection->load(1);
        
        $test = new MysqlStorage();
        $test->setCollection($collection);
        
        $test->dispatch('promote',TestParent::class);
    }
    
}