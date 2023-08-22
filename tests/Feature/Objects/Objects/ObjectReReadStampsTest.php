<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;

class ObjectReReadStampsTest extends DatabaseTestCase
{
    
    public function testTimestamps() {
        $add = new Dummy();
        $add->dummyint = 123;
        $add->commit();
        Objects::flushCache();
        $read = Objects::load($add->getID());
        $this->assertFalse(is_null($read->created_at));
    }
}
