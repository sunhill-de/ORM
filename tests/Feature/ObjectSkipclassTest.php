<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class SkipClass extends \Sunhill\Test\ts_dummy {
    
}

class ObjectSkipclassTest extends ObjectCommon
{

    public function testSkipclass() {
	    $init_object = new SkipClass;
        $init_object->dummyint = 1243;
        $init_object->commit();

        \Sunhill\Objects\oo_object::flush_cache();
        $read_object = \Sunhill\Objects\oo_object::load_object_of($init_object->get_id());
        $this->assertEquals(1243,$read_object->dummyint);
        $read_object->dummyint = 4312;
        $read_object->commit();
        
        \Sunhill\Objects\oo_object::flush_cache();
        $reread_object = \Sunhill\Objects\oo_object::load_object_of($init_object->get_id());
        $this->assertEquals(4312,$reread_object->dummyint);
        
	}
	
}
