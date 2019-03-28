<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectDummy3Test extends ObjectCommon
{
	public function testDummy() {
	   $test = new \Sunhill\Test\ts_dummy();
	   $test->dummyint = 1;
	   $tag = new \Sunhill\Objects\oo_tag('TagA',true);
	   $test->tags->stick($tag);
       $test->commit();
       
       \Sunhill\Objects\oo_object::flush_cache();
       $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
       $tag = new \Sunhill\Objects\oo_tag('TagB',true);
       $read->tags->stick($tag);
       $read->commit();
	   
       \Sunhill\Objects\oo_object::flush_cache();
       $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
       $this->assertEquals('TagB',$reread->tags[1]->get_fullpath());
	}
}
