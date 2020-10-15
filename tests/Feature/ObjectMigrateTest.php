<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Facades\Objects;

class testA extends oo_object {
   
    public static $object_infos = [
        'name'=>'testA',            // A repetition of static:$object_name @todo see above
        'table'=>'testA',         // A repitition of static:$table_name
        'name_s'=>'Migrationtest A object',   // A human readable name in singular
        'name_p'=>'Migrationtest A objects',  // A human readable name in plural
        'description'=>'For migration tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'testA';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('testint');
        self::varchar('testchar');
    }
    
}

class testD extends \Sunhill\ORM\Test\ts_dummy {

    public static $object_infos = [
        'name'=>'testD',            // A repetition of static:$object_name @todo see above
        'table'=>'testD',         // A repitition of static:$table_name
        'name_s'=>'Migrationtest D object',   // A human readable name in singular
        'name_p'=>'Migrationtest D objects',  // A human readable name in plural
        'description'=>'For migration tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'testD';
    
    public static $type='varchar';
    
    protected static function setup_properties() {
        $method = self::$type;
        parent::setup_properties();
        if ($method == 'enum') {
            self::enum('testfield')->set_enum_values(['A','B']);  
        } else {
            self::$method('testfield');
        }
    }
    
}

class testE extends oo_object {

    public static $object_infos = [
        'name'=>'testE',            // A repetition of static:$object_name @todo see above
        'table'=>'testE',         // A repitition of static:$table_name
        'name_s'=>'Migrationtest e object',   // A human readable name in singular
        'name_p'=>'Migrationtest e objects',  // A human readable name in plural
        'description'=>'For migration tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'testE';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::arrayofobjects('testfield')->set_allowed_objects(["\Sunhill\ORM\Test\\ts_dummy"]);
    }
    
}

class ObjectMigrateTest extends DBTestCase
{
    
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
        testA::migrate();
        $test = new testA();
        $test->testint = 123;
        $test->testchar = 'AAA';
        $test->commit();
        
        $reread = Objects::load($test->get_id());
        $this->assertEquals('AAA',$reread->testchar);
    }

    public function testRemovedField1() {
        DB::statement('drop table if exists testA');
        DB::statement('create table testA (id int primary key,testint int,testchar varchar(100),additional int)');
        testA::migrate();
        $test = new testA();
        $test->testint = 123;
        $test->commit();
        
        $reread = Objects::load($test->get_id());
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
        DB::statement('select additional from testA where id = '.$test->get_id());
        $this->fail('Fehler wurde nicht ausgelöst');
    }
    
    public function testAlterType() {
        DB::statement('drop table if exists testA');
        DB::statement('create table testA (id int primary key,testint int,testchar int,additional int)');
        testA::migrate();
        $test = new testA();
        $test->testchar = 'ABC';
        $test->commit();
        
        $reread = Objects::load($test->get_id());
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
       Objects::flush_cache();
        $read = Objects::load($test->get_id());
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
       Objects::flush_cache();
        $read = Objects::load($test->get_id());
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
        $test = new TestE();
        $dummy = new \Sunhill\ORM\Test\ts_dummy;
        $dummy->dummyint = 2;
        $test->testfield[] = $dummy;
        $test->commit();
        Objects::flush_cache();
        $read = Objects::load($test->get_id());
        $this->assertEquals($read->testfield[0]->dummyint,2);
    }
}
