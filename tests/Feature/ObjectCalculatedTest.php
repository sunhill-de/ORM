<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Tests\DBTestCase;

class TestClass extends oo_object {

    public static $object_infos = [
        'name'=>'TestClass',            // A repetition of static:$object_name @todo see above
        'table'=>'dummies',         // A repitition of static:$table_name
        'name_s'=>'Hookingtest A object',   // A human readable name in singular
        'name_p'=>'Hookingtest A objects',  // A human readable name in plural
        'description'=>'For hooking tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $table_name = 'dummies';
    
    public $return = 'ABC';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('dummyint');
        self::calculated('calcfield');
    }
    
    public function calculate_calcfield() {
        return $this->return;
    }
    
    public function set_return($value) {
        $this->return = $value;
        $this->recalculate();
    }
}

class ObjectCalculatedTest extends DBTestCase
{

    public function testReadCalculated() {
        $test = new TestClass;
  //      $test->recalculate();
        $this->assertEquals('ABC',$test->calcfield);
    }
    
    public function testFailWritingCalculated() {
        $this->expectException(\Sunhill\ORM\Objects\ObjectException::class);
        $test = new TestClass;
        $test->calcfield = 'DEF';
    }
    
    public function testCacheCalculated() {
        $test = new TestClass;
        $test->dummyint = 1;
   //     $test->recalcualate();
        $test->commit();
        $hilf = DB::table('caching')->select('value')->where('object_id','=',$test->get_id())->where('fieldname','=','calcfield')->first();
        $this->assertEquals('ABC',$hilf->value);
    }
    
    public function testChangeCache() {
        $test = new TestClass;
        $test->dummyint = 1;
        $test->set_return('ABC');
        $test->commit();
        \Sunhill\ORM\Objects\oo_object::flush_cache();
        $read = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());
        $read->set_return('DEF');
        $read->commit();
        $hilf = DB::table('caching')->select('value')->where('object_id','=',$test->get_id())->where('fieldname','=','calcfield')->first();
        $this->assertEquals('DEF',$hilf->value);
    }
    
    public function testChangeCalc() {
        $test = new TestClass;
        $test->dummyint = 1;
        $test->set_return('ABC');
        $test->commit();
        \Sunhill\ORM\Objects\oo_object::flush_cache();
        $read = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals('ABC',$read->calcfield);
        $read->set_return('DEF');
        $this->assertEquals('DEF',$read->calcfield);
    }
}
