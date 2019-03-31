<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectReReadStampsTest extends ObjectCommon
{

    public function testTimestamps() {
        $add = new \Sunhill\Test\ts_dummy();
        $add->dummyint = 123;
        $add->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($add->get_id());
        $this->assertFalse(is_null($read->created_at));
    }
}
