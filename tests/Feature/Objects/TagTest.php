<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Facades\Objects;

class TagTest extends DatabaseTestCase
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
	//    Tags::query()->where('id',1)->delete();
	    $tag = Tags::deleteTag(1);
	    Objects::flushCache();
	    $object = Objects::load($object->getID());
	    $this->assertEquals(0,count($object->tags));
	}
	
}
