<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Objects\Tag;
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
	    $tag = Tag::searchTag('TagA');
	    $object->tags->stick($tag);
	    $object->commit();
	    $tag = Tag::deleteTag('TagA');
	    Objects::flushCache();
	    $object = Objects::load($object->getID());
	    $this->assertEquals(0,count($object->tags));
	}
	
}
