<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\PropertyException;

class TestClass extends ORMObject {

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
    
    protected static function setupProperties() {
        parent::setupProperties();
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
        $this->expectException(PropertyException::class);
        $test = new TestClass;
        $test->calcfield = 'DEF';
    }
    
    public function testCacheCalculated() {
        Classes::registerClass(TestClass::class);
        $test = new TestClass;
        $test->dummyint = 1;
   //     $test->recalcualate();
        $test->commit();
        $hilf = DB::table('caching')->select('value')->where('object_id','=',$test->getID())->where('fieldname','=','calcfield')->first();
        $this->assertEquals('ABC',$hilf->value);
    }
    
    public function testChangeCache() {
        Classes::registerClass(TestClass::class);
        $test = new TestClass;
        $test->dummyint = 1;
        $test->set_return('ABC');
        $test->commit();
        Objects::flushCache();
        $read = Objects::load($test->getID());
        $read->set_return('DEF');
        $read->commit();
        $hilf = DB::table('caching')->select('value')->where('object_id','=',$test->getID())->where('fieldname','=','calcfield')->first();
        $this->assertEquals('DEF',$hilf->value);
    }
    
    public function testChangeCalc() {
        Classes::registerClass(TestClass::class);
        $test = new TestClass;
        $test->dummyint = 1;
        $test->set_return('ABC');
        $test->commit();
        Objects::flushCache();
        $read = Objects::load($test->getID());
        $this->assertEquals('ABC',$read->calcfield);
        $read->set_return('DEF');
        $this->assertEquals('DEF',$read->calcfield);
    }
}
