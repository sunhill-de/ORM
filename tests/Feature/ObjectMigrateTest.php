<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\Objects\oo_object;

class testA extends oo_object {
   
    public static $table_name = 'testA';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('testint');
        self::varchar('testchar');
        self::varchar('newfield');
    }
    
}

class testB extends oo_object {

    public static $table_name = 'testB';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('testint');
    }
        
}

class testC extends oo_object {
    
    public static $table_name = 'testC';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::varchar('testfield');
    }
    
}

class testD extends \Sunhill\Test\ts_dummy {

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

    public static $table_name = 'testE';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::arrayofobjects('testfield')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"]);
    }
    
}

class ObjectMigrateTest extends ObjectCommon
{
    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_table('testA',['testint int','testchar varchar(255)']);
        $this->create_table('testB',['testint int','testchar varchar(255)']);
        $this->create_table('testC',['testfield int']);
        $this->create_table('testD',[]);
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
        testA::migrate();
        $test = new testA();
        $test->testint = 123;
        $test->testchar = 'AAA';
        $test->newfield = 'ABC';
        $test->commit();
        
        $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals('ABC',$reread->newfield);
    }

    public function testRemovedField1() {
        $this->prepare_tables();
        testB::migrate();
        $test = new testB();
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
        testB::migrate();
        $test = new testB();
        $test->testint = 123;
        $test->commit();
        DB::statement('select testchar from testB where id = '.$test->get_id());
        $this->fail('Fehler wurde nicht ausgelöst');
    }
    
    public function testAlterType() {
        $this->prepare_tables();
        testC::migrate();
        $test = new testC();
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
        testD::$type = $type;
        testD::migrate();
        $test = new testD($type);
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
        testD::$type = $type;
        DB::statement('drop table testD');
        testD::migrate();
        $test = new testD($type);
        $test->dummyint = 1;
        $test->testfield = $init;
        $test->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals($read->testfield,$init);        
    }
    
    /**
     * @expectedException Illuminate\Database\QueryException
     */
    public function testNewInheritedFields() {
        $this->prepare_tables();
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
        testE::migrate();
        $test = new TestE();
        $dummy = new \Sunhill\Test\ts_dummy;
        $dummy->dummyint = 2;
        $test->testfield[] = $dummy;
        $test->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals($read->testfield[0]->dummyint,2);
    }
}
