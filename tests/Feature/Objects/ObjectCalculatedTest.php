<?php

namespace Sunhill\ORM\Tests\Feature\Objects;

use Illuminate\Support\Facades\DB;

use Sunhill\ORM\Tests\Testobjects\CalcClass;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Properties\PropertyException;

class ObjectCalculatedTest extends DatabaseTestCase
{

    public function testReadCalculated() {
        $test = new CalcClass;
  //      $test->recalculate();
        $this->assertEquals('ABC',$test->calcfield);
    }
    
    public function testFailWritingCalculated() {
        $this->expectException(PropertyException::class);
        $test = new CalcClass;
        $test->calcfield = 'DEF';
    }
    
    public function testCacheCalculated() {
        Classes::flushClasses();
        Classes::registerClass(CalcClass::class);
        
        $test = new CalcClass;
        $test->dummyint = 1;
   //     $test->recalcualate();
        $test->commit();
        $hilf = DB::table('calcclasses_calc_calcfield')->select('value')->where('id',$test->getID())->where('fieldname','=','calcfield')->first();
        $this->assertEquals('ABC',$hilf->value);
    }
    
    public function testChangeCache() {
        $test = new CalcClass;
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
        $test = new CalcClass;
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
