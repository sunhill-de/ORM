<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class testA extends \Sunhill\Objects\oo_object {
   
    public static $table_name = 'testA';
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('testint');
        $this->varchar('testchar');
        $this->varchar('newfield');
    }
    
}

class testB extends \Sunhill\Objects\oo_object {

    public static $table_name = 'testB';
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('testint');
    }
        
}

class ObjectMigrateTest extends ObjectCommon
{
    protected function prepare_tables() {
        DB::statement("drop table if exists testA");
        DB::statement("drop table if exists testB");
        DB::statement("create table testA (id int primary key,testint int,testchar varchar(255))");
        DB::statement("create table testB (id int primary key,testint int,testchar varchar(255))");
    }
    
    /**
     * @expectedException \Exception
     */
    public function testSanity() {
        $this->prepare_tables();
        $test = new testA();
        $test->testint = 123;
        $test->testchar = 'AAA';
        $test->newfield = 'ABC';
        $test->commit();
    }
    
    public function testNewField() {
        $this->prepare_tables();
        $test = new testA();
        $test->migrate();
        $test->testint = 123;
        $test->testchar = 'AAA';
        $test->newfield = 'ABC';
        $test->commit();
        
        $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals('ABC',$reread->newfield);
    }

    public function testRemovedField1() {
        $this->prepare_tables();
        $test = new testB();
        $test->migrate();
        $test->testint = 123;
        $test->commit();
        
        $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals(123,$reread->testint);
    }
    
    /**
     * @expectedException \Exception
     */
    public function testRemovedField2() {
        $this->prepare_tables();
        $test = new testB();
        $test->migrate();
        $test->testint = 123;
        $test->commit();
        DB::statement('select testchar from testB where id = '.$test->get_id());
        $this->fail('Fehler wurde nicht ausgelöst');
    }
}
