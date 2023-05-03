<?php

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class MysqlStorageDeleteTest extends DatabaseTestCase
{
    
    public function testDummy()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        
        $test->delete(1);
        
        $this->assertDatabaseMissing('objects', ['id'=>1]);
        $this->assertDatabaseMissing('dummies', ['id'=>1]);
        $this->assertDatabaseMissing('tagobjectassigns', ['container_id'=>1]);
        $this->assertDatabaseMissing('attributevalues', ['object_id'=>1]);
    }

    public function testDummyChildLittleMoreComplexDelete()
    {
        $object = new DummyChild();
        $test = new MysqlStorage($object);
        
        $test->delete(8);
        $this->assertDatabaseMissing('dummychildren', ['id'=>8]);
        $this->assertDatabaseMissing('dummies', ['id'=>8]);
        $this->assertDatabaseMissing('objects', ['id'=>8]);
    }
    
    public function testTestParent()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        
        $test->delete(9);
        
        $this->assertDatabaseMissing('testparents',['id'=>9]);
        $this->assertDatabaseMissing('testparents_array_parentoarray', ['id'=>9]);
        $this->assertDatabaseMissing('testparents_array_parentsarray', ['id'=>9]);
        $this->assertDatabaseMissing('testparents_calc_parentcalc', ['id'=>9]);
    }
    
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage($object);
        
        $test->delete(18);

        $this->assertDatabaseMissing('testparents',['id'=>18]);
        $this->assertDatabaseMissing('testparents_array_parentoarray', ['id'=>18]);
        $this->assertDatabaseMissing('testparents_array_parentsarray', ['id'=>18]);
        $this->assertDatabaseMissing('testparents_calc_parentcalc', ['id'=>18]);
        
        $this->assertDatabaseMissing('testchildren',['id'=>18]);
        $this->assertDatabaseMissing('testchildren_array_childoarray', ['id'=>18]);
        $this->assertDatabaseMissing('testchildren_array_childsarray', ['id'=>18]);
        $this->assertDatabaseMissing('testchildren_calc_childcalc', ['id'=>18]);
        
    }
}