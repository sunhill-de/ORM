<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test\ts_dummy;

class SkipClass extends ts_dummy {
    
    public static $table_name = 'skipclasses';
}

class ObjectSkipclassTest extends ObjectCommon
{

    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_table('skipclasses', []);
        $this->create_write_scenario();
    }
    
    public function testSkipclass() {
        $this->prepare_tables();
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
