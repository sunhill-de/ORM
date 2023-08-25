<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;

class ObjectDeleteTest extends DatabaseTestCase
{
    public function testInsertDummy()
    {        
        Dummy::delete(1);

        $this->assertDatabaseMissing('objects',['id'=>1]);
        $this->assertDatabaseMissing('dummies',['id'=>1]);
        $this->assertDatabaseMissing('attributeobjectassigns',['object_id'=>1]);
        $this->assertDatabaseMissing('tagobjectassigns',['object_id'=>1]);        
        $this->assertDatabaseMissing('objectobjectassigns',['target_id'=>1]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>1]);
    }
    
    public function testInsertTestParent()
    {
        TestParent::delete(9);
        
        $this->assertDatabaseMissing('objects',['id'=>9]);
        $this->assertDatabaseMissing('testparents',['id'=>9]);
        $this->assertDatabaseMissing('testparents_parentoarray',['id'=>9]);
        $this->assertDatabaseMissing('testparents_parentsarray',['id'=>9]);
        $this->assertDatabaseMissing('testparents_parentmap',['id'=>9]);
        $this->assertDatabaseMissing('attributeobjectassigns',['object_id'=>9]);
        $this->assertDatabaseMissing('tagobjectassigns',['object_id'=>9]);
        $this->assertDatabaseMissing('objectobjectassigns',['target_id'=>9]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>9]);
    }
    
    public function testInsertTestChild()
    {
        TestChild::delete(17);

        $this->assertDatabaseMissing('objects',['id'=>17]);
        $this->assertDatabaseMissing('testparents',['id'=>17]);
        $this->assertDatabaseMissing('testchildren',['id'=>17]);
        $this->assertDatabaseMissing('testparents_parentoarray',['id'=>17]);
        $this->assertDatabaseMissing('testparents_parentsarray',['id'=>17]);
        $this->assertDatabaseMissing('testparents_parentmap',['id'=>17]);
        $this->assertDatabaseMissing('testchildren_childoarray',['id'=>17]);
        $this->assertDatabaseMissing('testchildren_childsarray',['id'=>17]);
        $this->assertDatabaseMissing('testchildren_childmap',['id'=>17]);
        $this->assertDatabaseMissing('attributeobjectassigns',['object_id'=>17]);
        $this->assertDatabaseMissing('tagobjectassigns',['object_id'=>17]);
        $this->assertDatabaseMissing('objectobjectassigns',['target_id'=>17]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>17]);
        
    }
    
    public function testDeleteDummyChild()
    {
        DummyChild::delete(5);        

        $this->assertDatabaseMissing('objects',['id'=>5]);
        $this->assertDatabaseMissing('dummies',['id'=>5]);
        $this->assertDatabaseMissing('dummychildren',['id'=>5]);
        $this->assertDatabaseMissing('attributeobjectassigns',['object_id'=>5]);
        $this->assertDatabaseMissing('tagobjectassigns',['object_id'=>5]);
        $this->assertDatabaseMissing('objectobjectassigns',['target_id'=>5]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>5]);
        
    }
    
    public function testDeleteReferenceOnly()
    {
        ReferenceOnly::delete(27);        

        $this->assertDatabaseMissing('objects',['id'=>27]);
        $this->assertDatabaseMissing('referenceonlies',['id'=>27]);
        $this->assertDatabaseMissing('attributeobjectassigns',['object_id'=>27]);
        $this->assertDatabaseMissing('tagobjectassigns',['object_id'=>27]);
        $this->assertDatabaseMissing('objectobjectassigns',['target_id'=>27]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>27]);
    }
    
    public function testDeleteSecondLevelChild()
    {
        SecondLevelChild::delete(32);
    
        $this->assertDatabaseMissing('objects',['id'=>32]);
        $this->assertDatabaseMissing('secondlevelchildren',['id'=>32]);
        $this->assertDatabaseMissing('referenceonlies',['id'=>32]);
        $this->assertDatabaseMissing('attributeobjectassigns',['object_id'=>32]);
        $this->assertDatabaseMissing('tagobjectassigns',['object_id'=>32]);
        $this->assertDatabaseMissing('objectobjectassigns',['target_id'=>32]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>32]);
    }
    
    public function testDeleteSimpleChild()
    {
        TestSimpleChild::delete(25);        
        $this->assertDatabaseMissing('objects',['id'=>25]);
        $this->assertDatabaseMissing('testsimplechildren',['id'=>25]);
        $this->assertDatabaseMissing('testparents',['id'=>25]);
        $this->assertDatabaseMissing('attributeobjectassigns',['object_id'=>25]);
        $this->assertDatabaseMissing('tagobjectassigns',['object_id'=>25]);
        $this->assertDatabaseMissing('objectobjectassigns',['target_id'=>25]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>25]);
    }
}
