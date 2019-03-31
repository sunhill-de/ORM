<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectDummyTest extends ObjectCommon
{
	public function testDummy() {
	   $test = new \Sunhill\Test\ts_dummy();
	   $test->dummyint = 3;
	   $test->commit();
	   $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
	   $read->dummyint = 4;
	   $read->commit();
	   $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());	   
	   $this->assertEquals(4,$reread->dummyint);
	}
}
