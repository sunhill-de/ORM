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

    protected static $hook_str = '';
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('hooking_int')->set_model('\Tests\Feature\Hooking')->set_default(0);
        $this->varchar('hookstate')->set_model('\Tests\Feature\Hooking')->set_default('');
        $this->object('hooked_object')->set_allowed_objects(['\\Tests\\Feature\\HookedObject']);
        $this->arrayofstrings('strarray');
    }
    
    protected function setup_hooks() {
        parent::setup_hooks();
    }
    
    protected function field_changed($params) {
        $this->hookstate = '('.$params['subaction'].':'.$params['FROM']."=>".$params['TO'].")";
    }
    
    protected function sarray_changed($diff) {
        $hilf = '(sarray:NEW:';
        foreach ($diff['NEW'] as $new) {
            $hilf .= $new;
        }
        $hilf .= ' REMOVED:';
        foreach ($diff['REMOVED'] as $new) {
            $hilf .= $new;
        }
        $this->hookstate = $hilf.')';
    }
    
    public function get_hook_str() {
        return $this->hookstate;
    }
    
}

class ObjectHookTest extends ObjectCommon
{
    protected function setupHookTables() {
        DB::statement("drop table if exists hookeds ");
        DB::statement("drop table if exists hookings ");
        DB::statement("create table hookeds (id int primary key,hooked_int int)");
        DB::statement("create table hookings (id int primary key,hooking_int int,hookstate varchar(100))");       
    }
    
    /**
     * @dataProvider HooksProvider
     * @param unknown $init_hook
     * @param unknown $change_hook
     * @param unknown $excpect
     */
    public function testHooks($init_hook,$change_hook,$expect) {
        $this->setupHookTables();
        $test = $init_hook();
        $test->commit();
        
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $change_hook($read);
        $read->commit();
        
        $this->assertEquals($expect,$read->get_hook_str());
    }
    
    public function HooksProvider() {
        return [
            [function() {
                $result = new HookingObject();
                $result->hooking_int = 222;
                 return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','field_changed','hooking_int');
                $change->hooking_int = 333;
            },'(hooking_int:222=>333)'],
            
            [function() {
                $result = new HookingObject();
                $result->strarray[] = 'ABC';
                return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','sarray_changed','strarray');
                $change->strarray[] = 'DEF';
            },'(sarray:NEW:DEF REMOVED:)'],
            ];
    }
    
    /**
     * @group complex
     */
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
        $this->assertEquals('(Hooked:123=>234)',$readhooking->hookstate);
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
        $this->assertEquals('[123=>234]',$readhooking->hookstate);
    }
    
    
}
