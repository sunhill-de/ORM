<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Objects\oo_tag;
use Sunhill\ORM\Facades\Objects;

class TagTest extends DBTestCase
{
       
	/**
	 * @group static
     * Dieser Test wurde in Feature ausgelager, da er von update-commits abhängig ist
	 */
	public function testStaticDeleteTagObjects() {
	    $object = new ts_dummy(); 
	    $object->dummyint = 1;
	    $tag = oo_tag::search_tag('TagA');
	    $object->tags->stick($tag);
	    $object->commit();
	    $tag = oo_tag::delete_tag('TagA');
	    Objects::flush_cache();
	    $object = Objects::load($object->get_id());
	    $this->assertEquals(0,count($object->tags));
	}
	
}
