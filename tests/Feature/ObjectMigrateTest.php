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

class testC extends \Sunhill\Objects\oo_object {
    
    public static $table_name = 'testC';
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->varchar('testfield');
    }
    
}

class testD extends \Sunhill\Test\ts_dummy {

    public static $table_name = 'testD';
    
    protected $type;
    
    public function __construct($type='varchar') {
        $this->type = $type;
        parent::__construct();
    }
    
    protected function setup_properties() {
        $method = $this->type;
        parent::setup_properties();
        if ($method == 'enum') {
           $this->enum('testfield')->set_enum_values(['A','B']);  
        } else {
            $this->$method('testfield');
        }
    }
    
}

class ObjectMigrateTest extends ObjectCommon
{
    protected function prepare_tables() {
        DB::statement("drop table if exists testA");
        DB::statement("drop table if exists testB");
        DB::statement("drop table if exists testC");
        DB::statement("drop table if exists testD");
        DB::statement("create table testA (id int primary key,testint int,testchar varchar(255))");
        DB::statement("create table testB (id int primary key,testint int,testchar varchar(255))");
        DB::statement("create table testC (id int primary key,testfield int)");
        DB::statement("create table testD (id int primary key)");
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
    
    public function testAlterType() {
        $this->prepare_tables();
        $test = new testC();
        $test->migrate();
        $test->testfield = 'ABC';
        $test->commit();
        
        $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals('ABC',$reread->testfield);        
    }
    
    /**
     * @dataProvider FieldTypeProvider
     */
    public function testFieldType($type,$init) {
        $this->prepare_tables();
        $test = new testD($type);
        $test->migrate();
        $test->dummyint = 1;
        $test->testfield = $init;
        $test->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals($read->testfield,$init);
    }

    /**
     * @dataProvider FieldTypeProvider
     * @param unknown $type
     * @param unknown $init
     */
    public function testNewTable($type,$init) {
        $this->prepare_tables();
        DB::statement('drop table testD');
        $test = new testD($type);
        $test->migrate();
        $test->dummyint = 1;
        $test->testfield = $init;
        $test->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals($read->testfield,$init);        
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
}
