<?php

namespace Sunhill\ORM\tests\Feature\Objects\Utils;

use Illuminate\Foundation\testing\WithFaker;
use Illuminate\Foundation\testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use Sunhill\ORM\Objects\ORMObject;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;

use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\TestParent;

class ObjectMigrateTest extends DatabaseTestCase
{

    public function testSanity() {
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key,dummyint int)');
        $this->expectException(\Exception::class);
        $test = new Dummy();
        $test->testint = 123;
        $test->testchar = 'AAA';
        $test->commit();
    }
    
    public function testMissingTable()
    {
        DB::statement('drop table if exists dummies');
        $this->assertDatabaseHasNotTable('dummies');
        
        Classes::migrateClass('dummy');
        
        $this->assertDatabaseHasTable('dummies');
    }
    
    public function testNewField() {
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key)');
        
        Classes::migrateClass('dummy');
        
        $test = new Dummy();
        $test->dummyint = 123;
        $test->commit();
        
        $reread = Objects::load($test->getID());
        $this->assertEquals(123,$reread->dummyint);
    }

    public function testRemovedField1() {
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key,dummyint int, additional int)');
        Dummy::migrate();
        $test = new Dummy();
        $test->dummyint = 123;
        $test->commit();
        
        $reread = Objects::load($test->getID());
        $this->assertEquals(123,$reread->dummyint);
    }
    
    public function testRemovedField2() {
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key,dummyint int, additional int)');
        $this->expectException(\Exception::class);
        Dummy::migrate();
        $test = new Dummy();
        $test->dummyint = 123;
        $test->commit();
        DB::statement('select additional from testA where id = '.$test->getID());
    }
   /** Test removed by now because we have some conflict with the test sqlite table that doesn't support alter 
    * table so good. 
    public function testAlterType() {
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key,dummyint varchar(10))');
        Dummy::migrate();
        $test = new Dummy();
        $test->dummyint = 12;
        $test->commit();
        
        $reread = Objects::load($test->getID());
        $this->assertEquals(12,$reread->dummyint);        
    }
    */
    public function testPassthru() {
        DB::statement('drop table if exists referenceonlies');
        ReferenceOnly::migrate();
        $test = new ReferenceOnly();
        $dummy = new Dummy;
        $dummy->dummyint = 2;
        $test->testoarray[] = $dummy;
        $test->commit();
        Objects::flushCache();
        $read = Objects::load($test->getID());
        $this->assertEquals($read->testoarray[0]->dummyint,2);
    }
}
