<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\DBTestCase;
use Sunhill\Objects\oo_object;
 
class TagTest extends DBTestCase
{
       
	/**
	 * @group static
     * Dieser Test wurde in Feature ausgelager, da er von update-commits abhängig ist
	 */
	public function testStaticDeleteTagObjects() {
	    $object = new \Sunhill\Test\ts_dummy(); 
	    $object->dummyint = 1;
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagA');
	    $object->tags->stick($tag);
	    $object->commit();
	    $tag = \Sunhill\Objects\oo_tag::delete_tag('TagA');
	    \Sunhill\Objects\oo_object::flush_cache();
	    $object = \Sunhill\Objects\oo_object::load_object_of($object->get_id());
	    $this->assertEquals(0,count($object->tags));
	}
	
}
