<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectDummy2Test extends ObjectCommon
{
	public function testDummy() {
	   $test = new \Sunhill\Test\ts_referenceonly();
	   $obj1 = new \Sunhill\Test\ts_dummy();
	   $obj1->dummyint = 1;
	   $obj2 = new \Sunhill\Test\ts_dummy();
	   $obj2->dummyint = 2;
	   $obj3 = new \Sunhill\Test\ts_dummy();
	   $obj3->dummyint = 3;
	   $test->testobject = $obj1;
	   $test->testoarray[] = $obj2;
	   $test->testoarray[] = $obj3;
	   $test->testint = 666;
	   $test->commit();

	   $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
	   var_dump($read->testobject);
	   $read->testobject->dummyint = 11;
	   $read->testoarray[0]->dummyint = 22;
	   $read->testoarray[1]->dummyint = 33;
	   $obj4 = new \Sunhill\Test\ts_dummy();
	   $obj4->dummint = 44;
	   $read->testoarray[] = $obj4;
	   $read->commit();
	   
	   $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());	   
	   $this->assertEquals([11,22,33,44],
	       [$reread->testobject->dummyint,
	        $reread->testoarray[0]->dummyint,
	        $reread->testoarray[1]->dummyint,
	        $reread->testoarray[2]->dummyint
	   ]);
	}
}
