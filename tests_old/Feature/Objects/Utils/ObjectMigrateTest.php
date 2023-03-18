<?php

namespace Sunhill\ORM\tests\Feature\Objects\Utils;

use Illuminate\Foundation\testing\WithFaker;
use Illuminate\Foundation\testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use Sunhill\ORM\Objects\ORMObject;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;

use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\testA;
use Sunhill\ORM\Tests\Objects\testD;
use Sunhill\ORM\Tests\Objects\testE;

class ObjectMigrateTest extends DBTestCase
{

    public function setUp() : void {
        parent::setUp();    
        Classes::flushClasses();
        Classes::registerClass(testA::class);
        Classes::registerClass(testD::class);
        Classes::registerClass(testE::class);
        Classes::registerClass(Dummy::class);
    }
    
    public function testSanity() {
        DB::statement('drop table if exists testA');
        DB::statement('create table testA (id int primary key,testint int)');
        $this->expectException(\Exception::class);
        $test = new testA();
        $test->testint = 123;
        $test->testchar = 'AAA';
        $test->commit();
    }
    
    public function testNewField() {
        DB::statement('drop table if exists testA');
        DB::statement('create table testA (id int primary key,testint int)');
        Classes::migrateClass('testA');
        $test = new testA();
        $test->testint = 123;
        $test->testchar = 'AAA';
        $test->commit();
        
        $reread = Objects::load($test->getID());
        $this->assertEquals('AAA',$reread->testchar);
    }

    public function testRemovedField1() {
        DB::statement('drop table if exists testA');
        DB::statement('create table testA (id int primary key,testint int,testchar varchar(100),additional int)');
        testA::migrate();
        $test = new testA();
        $test->testint = 123;
        $test->commit();
        
        $reread = Objects::load($test->getID());
        $this->assertEquals(123,$reread->testint);
    }
    
    public function testRemovedField2() {
        DB::statement('drop table if exists testA');
        DB::statement('create table testA (id int primary key,testint int,testchar varchar(100),additional int)');
        $this->expectException(\Exception::class);
        testA::migrate();
        $test = new testA();
        $test->testint = 123;
        $test->commit();
        DB::statement('select additional from testA where id = '.$test->getID());
        $this->fail('Fehler wurde nicht ausgelöst');
    }
    
    public function testAlterType() {
        DB::statement('drop table if exists testA');
        DB::statement('create table testA (id int primary key,testint int,testchar int,additional int)');
        testA::migrate();
        $test = new testA();
        $test->testchar = 'ABC';
        $test->commit();
        
        $reread = Objects::load($test->getID());
        $this->assertEquals('ABC',$reread->testchar);        
    }
    
    /**
     * @dataProvider FieldTypeProvider
     */
    public function testFieldType($type,$init) {
        DB::statement('drop table if exists testD');
        DB::statement("create table testD (id int primary key)");
        testD::$type = $type;
        testD::migrate();
        $test = new testD($type);
        $test->dummyint = 1;
        $test->testfield = $init;
        $test->commit();
        Objects::flushCache();
        $read = Objects::load($test->getID());
        $this->assertEquals($read->testfield,$init);
    }

    /**
     * @dataProvider FieldTypeProvider
     * @param unknown $type
     * @param unknown $init
     */
    public function testNewTable($type,$init) {
        testD::$type = $type;
        DB::statement('drop table if exists testD');
        testD::migrate();
        $test = new testD($type);
        $test->dummyint = 1;
        $test->testfield = $init;
        $test->commit();
        Objects::flushCache();
        $read = Objects::load($test->getID());
        $this->assertEquals($read->testfield,$init);        
    }
    
    public function testNewInheritedFields() {
        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::statement('drop table testD');
        testD::$type = 'varchar';
        testD::migrate();
        $test = new testD();
        $test->dummyint = 1;
        $test->testfield = 'abc';
        $test->commit();
        $test = DB::select(DB::raw('select dummyint from testD'));
    }
    
    public function FieldTypeProvider() {
        return [
            ['integer',4],
            ['varchar','ABC'],
            ['float',3.2],
            ['date','2012-02-02'],
            ['time','11:11:11'],
            ['datetime','2012-02-02 11:11:11'],
            ['text','Lorem Ipsum'],
            ['enum','A']
        ];
        
    }
    
    public function testPassthru() {
        DB::statement('drop table if exists testE');
        DB::statement("create table testE (id int primary key)");
        testE::migrate();
        $test = new testE();
        $dummy = new Dummy;
        $dummy->dummyint = 2;
        $test->testfield[] = $dummy;
        $test->commit();
        Objects::flushCache();
        $read = Objects::load($test->getID());
        $this->assertEquals($read->testfield[0]->dummyint,2);
    }
}
