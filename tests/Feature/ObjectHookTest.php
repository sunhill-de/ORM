<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Hooked extends Model {
    protected $table = 'hookeds';
    
    public $timestamps = false;
    
}

class Hooking extends Model {
    protected $table = 'hookings';
    
    public $timestamps = false;
    
}
    
class HookedObject extends \Sunhill\Objects\oo_object {
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('hooked_int')->set_model('\Tests\Feature\Hooked');
    }
    
}

class HookingObject extends \Sunhill\Objects\oo_object  {

    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('hooking_int')->set_model('\Tests\Feature\Hooking');
        $this->varchar('state')->set_model('\Tests\Feature\Hooking')->set_default('');
        $this->object('hooked_object')->set_allowed_objects(['\\Tests\\Feature\\HookedObject']);
    }
    
    protected function setup_hooks() {
        parent::setup_hooks();
        $this->add_hook('FIELDCOMMITTING','int_comitted','hooking_int');
        $this->add_hook('FIELDCOMMITTING','hooked_comitted','hooked_object.hooked_int');
    }
    
    protected function int_comitted($params) {
        $this->state = "(".$params['from']."=>".$params['to'].")";
    }
    
    protected function hooked_comitted($params) {
        $this->state = "(Hooked:".$params['from']."=>".$params['to'].")";
    }
    
}

class ObjectHookTest extends ObjectCommon
{
    protected function setupHookTables() {
        DB::statement("drop table if exists hookeds ");
        DB::statement("drop table if exists hookings ");
        DB::statement("create table hookeds (id int primary key,hooked_int int)");
        DB::statement("create table hookings (id int primary key,hooking_int int,state varchar(100))");       
    }
    
    public function testSimpleHook() {
        $this->setupHookTables();
        $hooked = new HookedObject();
        $hooked->hooked_int = 123;
        $hooking = new HookingObject();
        $hooking->hooking_int = 222;
        $hooking->hooked_object = $hooked;
        $hooking->commit();
        
        \Sunhill\Objects\oo_object::flush_cache();
        $readhooking = \Sunhill\Objects\oo_object::load_object_of($hooking->get_id());
        $readhooking->hooking_int = 333;
        $readhooking->commit();
        $this->assertEquals('(222=>333)',$readhooking->state);       
    }
    
    public function testComplexHookPersistantDirect() {
        $this->setupHookTables();
        $hooked = new HookedObject();
        $hooked->hooked_int = 123;
        $hooking = new HookingObject();
        $hooking->hooking_int = 222;
        $hooking->hooked_object = $hooked;
        $hooking->commit();
        
        \Sunhill\Objects\oo_object::flush_cache();
        $readhooking = \Sunhill\Objects\oo_object::load_object_of($hooking->get_id());
        $readhooking->hooked_object->hooked_int = 234;
        $readhooking->commit();
        $this->assertEquals('(Hooked:123=>234)',$readhooking->state);
    }
    
    public function testComplexHookPersistantInDirect() {
        $this->setupHookTables();
        $hooked = new HookedObject();
        $hooked->hooked_int = 123;
        $hooking = new HookingObject();
        $hooking->hooking_int = 222;
        $hooking->hooked_object = $hooked;
        $hooking->commit();
        
        \Sunhill\Objects\oo_object::flush_cache();
        $readhooked = \Sunhill\Objects\oo_object::load_object_of($hooking->hooked_object->get_id());
        $readhooked->hooked_int = 234;
        $readhooked->commit();
        
        \Sunhill\Objects\oo_object::flush_cache();
        $readhooking = \Sunhill\Objects\oo_object::load_object_of($hooking->get_id());
        $this->assertEquals('[123=>234]',$readhooking->state);
    }
    
    
}
