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

    public static $table_name = 'hookings';
    
    protected static $hook_str = '';
    
    protected function setup_properties() {
        parent::setup_properties();
        $this->integer('hooking_int')->set_model('\Tests\Feature\Hooking')->set_default(0);
        $this->varchar('hookstate')->set_model('\Tests\Feature\Hooking')->set_default('');
        $this->object('ofield')->set_allowed_objects(['\\Sunhill\\Test\\ts_dummy']);
        $this->arrayofstrings('strarray');
        $this->arrayofobjects('objarray')->set_allowed_objects(['\\Sunhill\\Test\\ts_dummy']);
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
    
    protected function oarray_changed($diff) {
        $hilf = '(oarray:NEW:';
        foreach ($diff['NEW'] as $new) {
            $hilf .= $new->dummyint;
        }
        $hilf .= ' REMOVED:';
        foreach ($diff['REMOVED'] as $new) {
            $hilf .= $new->dummyint;
        }
        $this->hookstate = $hilf.')';        
    }
    
    protected function child_changed($params) {
        $this->hookstate = '(Cofield:'.$params['FROM']."->".$params['TO'].")";
        $this->commit();
    }
    
    protected function arraychild_changed($params) {
        $this->hookstate = '(Cobjarray:'.$params['FROM']."->".$params['TO'].")";
        $this->commit();        
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

            [function() { // Test bei Objekt-Felder
                $result = new HookingObject();
                return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','field_changed','ofield');
                $dummy = new \Sunhill\Test\ts_dummy();
                $dummy->dummyint = 123;
                $change->ofield = $dummy;
            },'(ofield:NULL=>123)'],
            
            [function() { // Test bei Objekt-Felder
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

            [function() {// Test bei Objekt-Array, Eintrag hinzugefügt
                $result = new HookingObject();
                $dummy = new \Sunhill\Test\ts_dummy();
                $dummy->dummyint = 123;
                $result->objarray[] = $dummy;
                return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','oarray_changed','objarray');
                $dummy = new \Sunhill\Test\ts_dummy();
                $dummy->dummyint = 345;
                $change->objarray[] = $dummy;
            },'(oarray:NEW:345 REMOVED:)'],
            
            [function() { // Test bei String-Array, Eintrag entfernt
                $result = new HookingObject();
                $dummy = new \Sunhill\Test\ts_dummy();
                $dummy->dummyint = 345;
                $result->objarray[] = $dummy;
                return $result;
            },
            function($change) {
                $change->add_hook('UPDATING_PROPERTY','oarray_changed','objarray');
                unset($change->objarray[0]);
            },'(oarray:NEW: REMOVED:345)'],
            
            ];
    }
    
    private function prepare_object_test() {
        $this->setupHookTables();
        $dummy = new \Sunhill\Test\ts_dummy();
        $dummy->dummyint = 123;
        $test = new HookingObject();
        $test->add_hook('UPDATED_PROPERTY', 'child_changed', 'ofield.dummyint');
        $test->ofield = $dummy;
        $test->commit();
        return [$dummy,$test];
    }
    
    /**
     * @group complex
     */
    public function testChildChangeObjectDirect() {
        list($dummy,$test) = $this->prepare_object_test();
        $dummy->dummyint = 234;
        $dummy->commit();
        $this->assertEquals('(Cofield:123->234)',$test->get_hook_str());
    }
    
    public function testChildChangeObjectIndirect() {
        list($dummy,$test) = $this->prepare_object_test();
        \Sunhill\Objects\oo_object::flush_cache();
        // Das folgende ist ein Kunstgriff, weil einen drüber der Cache geleert wurde
        \Sunhill\Objects\oo_object::load_id_called($test->get_id(), $test);
        $readdummy = \Sunhill\Objects\oo_object::load_object_of($dummy->get_id());
        $readdummy->dummyint = 234;
        $readdummy->commit();
        $this->assertEquals('(Cofield:123->234)',$test->get_hook_str());
        
    }
    
    public function testChildChangeObjectBothIndirect() {
        list($dummy,$test) = $this->prepare_object_test();
        \Sunhill\Objects\oo_object::flush_cache();
        $readdummy = \Sunhill\Objects\oo_object::load_object_of($dummy->get_id());
        $readdummy->dummyint = 234;
        $readdummy->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $readtest = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals('(Cofield:123->234)',$readtest->get_hook_str());
        
    }
    
    private function prepare_array_test() {
        $this->setupHookTables();
        $dummy1 = new \Sunhill\Test\ts_dummy();
        $dummy1->dummyint = 123;
        $dummy2 = new \Sunhill\Test\ts_dummy();
        $dummy2->dummyint = 666;
        $test = new HookingObject();
        $test->add_hook('UPDATED_PROPERTY', 'arraychild_changed', 'objarray.dummyint');
        $test->objarray[] = $dummy1;
        $test->objarray[] = $dummy2;
        $test->commit();
        return [$dummy1,$dummy2,$test];
    }
    
    /**
     * @group array
     */
    public function testChildChangeArrayDirect() {
        list($dummy1,$dummy2,$test) = $this->prepare_array_test();
        $dummy1->dummyint = 234;
        $dummy1->commit();
        $this->assertEquals('(Cobjarray:123->234)',$test->get_hook_str());
        
    }
    
    public function testChildChangeArrayIndirect() {
        list($dummy1,$dummy2,$test) = $this->prepare_array_test();
        \Sunhill\Objects\oo_object::flush_cache();
        // Das folgende ist ein Kunstgriff, weil einen drüber der Cache geleert wurde
        \Sunhill\Objects\oo_object::load_id_called($test->get_id(), $test);
        $readdummy = \Sunhill\Objects\oo_object::load_object_of($dummy1->get_id());
        $readdummy->dummyint = 234;
        $readdummy->commit();
        $this->assertEquals('(Cobjarray:123->234)',$test->get_hook_str());        
    }
    
    public function testChildChangeArrayBothIndirect() {
        list($dummy1,$dummy2,$test) = $this->prepare_array_test();
        \Sunhill\Objects\oo_object::flush_cache();
        $readdummy = \Sunhill\Objects\oo_object::load_object_of($dummy1->get_id());
        $readdummy->dummyint = 234;
        $readdummy->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $readtest = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
        $this->assertEquals('(Cobjarray:123->234)',$readtest->get_hook_str());
    }
}
