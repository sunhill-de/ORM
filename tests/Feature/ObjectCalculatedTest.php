<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;
use Sunhill\Objects\oo_object;
use Sunhill;

class TestClass extends \Sunhill\Objects\oo_object {

    public static $table_name = 'dummies';
    
    public $return = 'ABC';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::calculated('calcfield');
    }
    
    public function calculate_calcfield() {
        return $this->return;
    }
}

class ObjectCalculatedTest extends ObjectCommon
{

    public function testReadCalculated() {
        $test = new TestClass;
        $this->assertEquals('ABC',$test->calcfield);
    }
    
    /**
     * @expectedException \Sunhill\Objects\ObjectException
     */
    public function testFailWritingCalculated() {
        $test = new TestClass;
        $test->calcfield = 'DEF';
    }
    
    public function testCacheCalculated() {
        $test = new TestClass;
        $test->commit();
        $hilf = DB::table('caching')->select('value')->where('object_id','=',$test->get_id())->where('fieldname','=','calcfield')->first();
        $this->assertEquals('ABC',$hilf->value);
    }
    
    public function testChangeCache() {
        $test = new TestClass;
        $test->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $read->return = 'DEF';
        $read->commit();
        $hilf = DB::table('caching')->select('value')->where('object_id','=',$test->get_id())->where('fieldname','=','calcfield')->first();
        $this->assertEquals('DEF',$hilf->value);
    }
}
