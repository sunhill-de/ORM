<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;

class ObjectReReadStampsTest extends DBTestCase
{
    
    public function testTimestamps() {
        $add = new \Sunhill\ORM\Test\ts_dummy();
        $add->dummyint = 123;
        $add->commit();
        \Sunhill\ORM\Objects\oo_object::flush_cache();
        $read = \Sunhill\ORM\Objects\oo_object::load_object_of($add->get_id());
        $this->assertFalse(is_null($read->created_at));
    }
}
