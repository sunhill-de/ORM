<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\ReferenceOnly;

class RegressionTest extends DBTestCase
{
    public function testRegeression1() {
       $test = new ReferenceOnly();
	   $obj1 = new Dummy();
	   $obj1->dummyint = 1;
	   $obj2 = new Dummy();
	   $obj2->dummyint = 2;
	   $obj3 = new Dummy();
	   $obj3->dummyint = 3;
	   $test->testobject = $obj1;
	   $test->testoarray[] = $obj2;
	   $test->testoarray[] = $obj3;
	   $test->testint = 666;
	   $test->commit();
       
	   Objects::flushCache();
	   $read = Objects::load($test->getID());
	   $read->testobject->dummyint = 11;
	   $read->testoarray[0]->dummyint = 22;
	   $read->testoarray[1]->dummyint = 33;
	   $obj4 = new Dummy();
	   $obj4->dummyint = 44;
	   $read->testoarray[] = $obj4;
	   $read->commit();
	   
	   Objects::flushCache();	   
	   $reread = Objects::load($test->getID());	   
	   $this->assertEquals([11,22,33,44],
	       [$reread->testobject->dummyint,
	        $reread->testoarray[0]->dummyint,
	        $reread->testoarray[1]->dummyint,
	        $reread->testoarray[2]->dummyint
	   ]);
	}
	
	public function testRegression2() {
	    $test = new Dummy();
	    $test->dummyint = 1;
	    $tag = new \Sunhill\ORM\Objects\Tag('TagA',true);
	    $test->tags->stick($tag);
	    $test->commit();
	    
	   Objects::flushCache();
	    $read = Objects::load($test->getID());
	    $tag = new \Sunhill\ORM\Objects\Tag('TagB',true);
	    $read->tags->stick($tag);
	    $read->commit();
	    
	   Objects::flushCache();
	    $reread = Objects::load($test->getID());
	    $this->assertEquals('TagB',$reread->tags[1]);
	}

	public function testRegression3() {
	    $test = new Dummy();
	    $test->dummyint = 3;
	    $test->commit();
	    $read = Objects::load($test->getID());
	    $read->dummyint = 4;
	    $read->commit();
	    $reread = Objects::load($test->getID());
	    $this->assertEquals(4,$reread->dummyint);
	}
	
}
