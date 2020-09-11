<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;

class RegressionTest extends DBTestCase
{
    public function testRegeression1() {
       $test = new \Sunhill\ORM\Test\ts_referenceonly();
	   $obj1 = new \Sunhill\ORM\Test\ts_dummy();
	   $obj1->dummyint = 1;
	   $obj2 = new \Sunhill\ORM\Test\ts_dummy();
	   $obj2->dummyint = 2;
	   $obj3 = new \Sunhill\ORM\Test\ts_dummy();
	   $obj3->dummyint = 3;
	   $test->testobject = $obj1;
	   $test->testoarray[] = $obj2;
	   $test->testoarray[] = $obj3;
	   $test->testint = 666;
	   $test->commit();
       
	   \Sunhill\ORM\Objects\oo_object::flush_cache();
	   $read = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());
	   $read->testobject->dummyint = 11;
	   $read->testoarray[0]->dummyint = 22;
	   $read->testoarray[1]->dummyint = 33;
	   $obj4 = new \Sunhill\ORM\Test\ts_dummy();
	   $obj4->dummyint = 44;
	   $read->testoarray[] = $obj4;
	   $read->commit();
	   
	   \Sunhill\ORM\Objects\oo_object::flush_cache();	   
	   $reread = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());	   
	   $this->assertEquals([11,22,33,44],
	       [$reread->testobject->dummyint,
	        $reread->testoarray[0]->dummyint,
	        $reread->testoarray[1]->dummyint,
	        $reread->testoarray[2]->dummyint
	   ]);
	}
	
	public function testRegression2() {
	    $test = new \Sunhill\ORM\Test\ts_dummy();
	    $test->dummyint = 1;
	    $tag = new \Sunhill\ORM\Objects\oo_tag('TagA',true);
	    $test->tags->stick($tag);
	    $test->commit();
	    
	    \Sunhill\ORM\Objects\oo_object::flush_cache();
	    $read = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());
	    $tag = new \Sunhill\ORM\Objects\oo_tag('TagB',true);
	    $read->tags->stick($tag);
	    $read->commit();
	    
	    \Sunhill\ORM\Objects\oo_object::flush_cache();
	    $reread = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());
	    $this->assertEquals('TagB',$reread->tags[1]);
	}

	public function testRegression3() {
	    $test = new \Sunhill\ORM\Test\ts_dummy();
	    $test->dummyint = 3;
	    $test->commit();
	    $read = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());
	    $read->dummyint = 4;
	    $read->commit();
	    $reread = \Sunhill\ORM\Objects\oo_object::load_object_of($test->get_id());
	    $this->assertEquals(4,$reread->dummyint);
	}
	
}
