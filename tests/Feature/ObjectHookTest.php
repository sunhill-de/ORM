<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Hooking extends Model {
    protected $table = 'hookings';
    
    public $timestamps = false;
    
}
    
class HookingObject extends \Sunhill\Objects\oo_object  {

    protected static $hook_str = '';
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('hooking_int')->set_model('\Tests\Feature\Hooking')->set_default(0);
        $this->varchar('hookstate')->set_model('\Tests\Feature\Hooking')->set_default('');
        $this->object('ofield')->set_allowed_objects(['\\Sunhill\\Test\\ts_dummy']);
        $this->arrayofstrings('strarray');
    }
    
    protected function setup_hooks() {
        parent::setup_hooks();
    }
    
    protected function field_changed($params) {
        if (is_a($params['FROM'],'\\Sunhill\\Test\\ts_dummy') || is_a($params['TO'],'\\Sunhill\\Test\\ts_dummy') ) {
            $from = empty($params['FROM'])?'NULL':$params['FROM']->dummyint;
            $to = empty($params['TO'])?'NULL':$this->ofield->dummyint;
            $this->hookstate = '(ofield:'.$from.'=>'.$to.')';
        } else {
            $this->hookstate = '('.$params['subaction'].':'.$params['FROM']."=>".$params['TO'].")";
        }
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
        DB::statement("drop table if exists hookings ");
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
            [function() { // Einfacher Test bei simple-Field
                $result = new HookingObject();
                $result->hooking_int = 222;
                 return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','field_changed','hooking_int');
                $change->hooking_int = 333;
            },'(hooking_int:222=>333)'],
            
            [function() {// Test bei String-Array, Eintrag hinzugefügt
                $result = new HookingObject();
                $result->strarray[] = 'ABC';
                return $result;
            },
            function($change) { 
                $change->add_hook('UPDATING_PROPERTY','sarray_changed','strarray');
                $change->strarray[] = 'DEF';
            },'(sarray:NEW:DEF REMOVED:)'],
            
            [function() { // Test bei String-Array, Eintrag entfernt
                $result = new HookingObject();
                $result->strarray[] = 'ABC';
                return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','sarray_changed','strarray');
                unset($change->strarray[0]);
            },'(sarray:NEW: REMOVED:ABC)'],

            [function() { // Test bei Objekt-Feldner
                $result = new HookingObject();
                return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','field_changed','ofield');
                $dummy = new \Sunhill\Test\ts_dummy();
                $dummy->dummyint = 123;
                $change->ofield = $dummy;
            },'(ofield:NULL=>123)'],
            
            [function() { // Test bei Objekt-Feldner
                $result = new HookingObject();
                $dummy = new \Sunhill\Test\ts_dummy();
                $dummy->dummyint = 123;
                $result->ofield = $dummy;
                return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','field_changed','ofield');
                $change->ofield = null;
            },'(ofield:123=>NULL)'],            

            ];
    }
    
    
}
