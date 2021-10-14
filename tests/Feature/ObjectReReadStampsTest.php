<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Objects;

class ObjectReReadStampsTest extends DBTestCase
{
    
    public function testTimestamps() {
        $add = new \Sunhill\ORM\Tests\Objects\ts_dummy();
        $add->dummyint = 123;
        $add->commit();
        Objects::flushCache();
        $read = Objects::load($add->getID());
        $this->assertFalse(is_null($read->created_at));
    }
}
