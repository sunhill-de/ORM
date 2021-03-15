<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Objects;

class SkipClass extends ts_dummy {
    
    public static $object_infos = [
        'name'=>'SkipClass',            // A repetition of static:$object_name @todo see above
        'table'=>'skipclasses',         // A repitition of static:$table_name
        'name_s'=>'skip class object',   // A human readable name in singular
        'name_p'=>'skip class objects',  // A human readable name in plural
        'description'=>'Only for Skip Class tests',
        'options'=>0,               // Reserved for later purposes
    ];
    
    public static $table_name = 'skipclasses';
}

class ObjectSkipclassTest extends DBTestCase
{
    
    public function setUp():void {
        parent::setUp();
        DB::statement('drop table if exists skipclasses');
        DB::statement("create table skipclasses (id int primary key)");
    }
    
    
    public function testSkipclass() {
        $init_object = new SkipClass;
        $init_object->dummyint = 1243;
        $init_object->commit();

        Objects::flush_cache();
        $read_object = Objects::load($init_object->get_id());
        $this->assertEquals(1243,$read_object->dummyint);
        $read_object->dummyint = 4312;
        $read_object->commit();
        
        Objects::flush_cache();
        $reread_object = Objects::load($init_object->get_id());
        $this->assertEquals(4312,$reread_object->dummyint);
        
	}
	
}
