<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Facades\Objects;

class TagTest extends DBTestCase
{
       
	/**
	 * @group static
     * Dieser Test wurde in Feature ausgelager, da er von update-commits abhängig ist
	 */
	public function testStaticDeleteTagObjects() {
	    $object = new Dummy(); 
	    $object->dummyint = 1;
	    $tag = Tags::searchTag('TagA');
	    $object->tags->stick('TagA');
	    $object->commit();
	    $tag = Tags::deleteTag(1);
	    Objects::flushCache();
	    $object = Objects::load($object->getID());
	    $this->assertEquals(0,count($object->tags));
	}
	
}
