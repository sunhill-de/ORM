<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class TestModel extends Model {
    
    protected $table = 'tests';
    
    public $timestamps = false;
    
}


class TestObject extends \Sunhill\Objects\oo_object {
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('testint')->set_model('\\Tests\\Feature\\TestModel');
    }
    
    
}

class ObjectNamespaceTest extends ObjectCommon
{
    
    public function testSetNamespace() {
        DB::statement("drop table if exists tests");
        DB::statement("create table tests (id int primary key,testint int)");
        $test = new TestObject();
        $test->testint = 123;
        $test->commit();
        
        $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $read->testint = 321;
        $read->commit();
        
        $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals(321,$reread->testint);        
        DB::statement("drop table if exists tests");
    }
}
