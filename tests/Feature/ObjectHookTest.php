<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\Objects\Dummy;

class HookingObject extends ORMObject  {

    public static $table_name = 'hookings';
    
    public static $hook_str = '';
    
    public static $object_infos = [
        'name'=>'HookingObject',            // A repetition of static:$object_name @todo see above
        'table'=>'hookings',         // A repitition of static:$table_name
        'name_s'=>'Hookingtest A object',   // A human readable name in singular
        'name_p'=>'Hookingtest A objects',  // A human readable name in plural
        'description'=>'For hooking tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    protected static function setupProperties() {
        parent::setupProperties();
        self::integer('hooking_int')->setDefault(0);
        self::varchar('hookstate')->setDefault('');
        self::object('ofield')->setAllowedObjects(['\\Sunhill\\ORM\\Tests\\Objects\\Dummy']);
        self::arrayofstrings('strarray');
        self::arrayOfObjects('objarray')->setAllowedObjects(['\\Sunhill\\ORM\\Tests\\Objects\\Dummy']);
    }
    
    protected function setupHooks() {
        parent::setupHooks();
    }
    
    protected function field_changing($params) {
        if ($params['subaction'] == 'ofield') {
            if (is_int($params['FROM'])) {
                $params['FROM'] = Objects::load($params['FROM']);
            }
            if (is_int($params['TO'])) {
                $params['TO'] = Objects::load($params['TO']);
            }
        }
        if (is_a($params['FROM'],'\\Sunhill\\ORM\\Tests\\Objects\\Dummy') || is_a($params['TO'],'\\Sunhill\\ORM\\Tests\\Objects\\Dummy') ) {
            $from = empty($params['FROM'])?'NULL':$params['FROM']->dummyint;
            $to = empty($params['TO'])?'NULL':$this->ofield->dummyint;
            $this->hookstate = '(ofield:'.$from.'=>'.$to.')';
        } else {
            $this->hookstate = '('.$params['subaction'].':'.$params['FROM']."=>".$params['TO'].")";
        }
    }
    
    protected function field_changed($params) {
        if ($params['subaction'] == 'ofield') {
            if (is_int($params['FROM'])) {
                $params['FROM'] = Objects::load($params['FROM']);
            }
            if (is_int($params['TO'])) {
                $params['TO'] = Objects::load($params['TO']);
            }
        }
        if (is_a($params['FROM'],'\\Sunhill\\ORM\\Tests\\Objects\\Dummy') || is_a($params['TO'],'\\Sunhill\\ORM\\Tests\\Objects\\Dummy') ) {
            $from = empty($params['FROM'])?'NULL':$params['FROM']->dummyint;
            $to = empty($params['TO'])?'NULL':$this->ofield->dummyint;
            self::$hook_str = '(ofield:'.$from.'=>'.$to.')';
        } else {
            self::$hook_str = '('.$params['subaction'].':'.$params['FROM']."=>".$params['TO'].")";
        }
    }
    
    protected function sarray_changing($diff) {
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
    
    protected function sarray_changed($diff) {
        $hilf = '(sarray:NEW:';
        foreach ($diff['NEW'] as $new) {
            $hilf .= $new;
        }
        $hilf .= ' REMOVED:';
        foreach ($diff['REMOVED'] as $new) {
            $hilf .= $new;
        }
        self::$hook_str = $hilf.')';
    }
    
    protected function oarray_changing($diff) {
        $hilf = '(oarray:NEW:';
        foreach ($diff['NEW'] as $new) {
            if (is_int($new)) {
                $new = Objects::load($new);
            }
            $hilf .= $new->dummyint;
        }
        $hilf .= ' REMOVED:';
        foreach ($diff['REMOVED'] as $new) {
            if (is_int($new)) {
                $new = Objects::load($new);
            }
            $hilf .= $new->dummyint;
        }
        $this->hookstate = $hilf.')';        
    }
    
    protected function oarray_changed($diff) {
        $hilf = '(oarray:NEW:';
        foreach ($diff['NEW'] as $new) {
            if (is_int($new)) {
                $new = Objects::load($new);
            }
            $hilf .= $new->dummyint;
        }
        $hilf .= ' REMOVED:';
        foreach ($diff['REMOVED'] as $new) {
            if (is_int($new)) {
                $new = Objects::load($new);
            }
            $hilf .= $new->dummyint;
        }
        self::$hook_str = $hilf.')';
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
        $hilf = $this->hookstate;
        return $hilf;
    }
    
}

class HookingChild extends HookingObject {
 
    public static $object_infos = [
        'name'=>'HookingChild',            // A repetition of static:$object_name @todo see above
        'table'=>'childhookings',         // A repitition of static:$table_name
        'name_s'=>'Hookingtest A object',   // A human readable name in singular
        'name_p'=>'Hookingtest A objects',  // A human readable name in plural
        'description'=>'For hooking tests only',
        'options'=>0,               // Reserved for later purposes
    ];
    public static $child_hookstr;
    
    public static $table_name = 'childhookings';
    
    protected static function setupProperties() {
        parent::setupProperties();
        self::integer('childhooking_int')->setDefault(0);
        self::arrayOfObjects('childhooking_oarray')->setAllowedObjects(['\\Sunhill\\ORM\\Tests\\Objects\\Dummy']);
    }
    
    protected function setupHooks() {
        parent::setupHooks();
        $this->addHook('UPDATED_PROPERTY','childint_changed','childhooking_int');
        $this->addHook('UPDATED_PROPERTY','childoarray_changed','childhooking_oarray');
        
    }
    
    protected function childint_changed($diff) {
        self::$child_hookstr = '('.$diff['FROM'].'=>'.$diff['TO'].')';
    }
    
    protected function childoarray_changed($diff) {
        $hilf = '(oarray:NEW:';
        foreach ($diff['NEW'] as $new) {
            if (is_int($new)) {
                $new = Objects::load($new);
            }
            $hilf .= $new->dummyint;
        }
        $hilf .= ' REMOVED:';
        foreach ($diff['REMOVED'] as $new) {
            if (is_int($new)) {
                $new = Objects::load($new);
            }
            $hilf .= $new->dummyint;
        }
        self::$child_hookstr = $hilf.')';
    }
    
}

class ObjectHookTest extends DBTestCase
{
    protected function setupHookTables() {
        DB::statement("drop table if exists hookings ");
        DB::statement("drop table if exists childhookings ");
        DB::statement("create table hookings (id int primary key,hooking_int int,hookstate varchar(100))");       
        DB::statement("create table childhookings (id int primary key,childhooking_int int)");
        Classes::registerClass(HookingObject::class);
        Classes::registerClass(HookingChild::class);
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
        
        Objects::flushCache();
        $read = Objects::load($test->getID());
        $change_hook($read);
        $read->commit();
        
        $this->assertEquals($expect,$read->get_hook_str());
    }
    
    /**
     * @dataProvider HooksProvider
     * @param unknown $init_hook
     * @param unknown $change_hook
     * @param unknown $excpect
     */
    public function testInlineHooks($init_hook,$change_hook,$expect) {
        $this->setupHookTables();
        $test = $init_hook();
        $test->commit();
        $change_hook($test);
        $test->commit();        
        $this->assertEquals($expect,$test->get_hook_str());        
    }
    
    /**
     * @dataProvider PostHooksProvider
     * @param unknown $init_hook
     * @param unknown $change_hook
     * @param unknown $excpect
     */
    public function testPostHooks($init_hook,$change_hook,$expect) {
        $this->setupHookTables();
        $test = $init_hook();
        $test->commit();
        $change_hook($test);
        $test->commit();
        $this->assertEquals($expect,$test::$hook_str);
    }
    
    
    public function HooksProvider() {
        return [
            [function() { // Einfacher Test bei simple-Field
                $result = new HookingObject();
                $result->hooking_int = 222;
                 return $result;
            },
            function($change,$postfix='ING') {
                $change->addHook('UPDATING_PROPERTY','field_changing','hooking_int');
                $change->hooking_int = 333;
            },'(hooking_int:222=>333)'],
            
            [function() {// Test bei String-Array, Eintrag hinzugef??gt
                $result = new HookingObject();
                $result->strarray[] = 'ABC';
                return $result;
            },
            function($change,$postfix='ING') { 
                $change->addHook('UPDATING_PROPERTY','sarray_changing','strarray');
                $change->strarray[] = 'DEF';
            },'(sarray:NEW:DEF REMOVED:)'],
            
            [function() { // Test bei String-Array, Eintrag entfernt
                $result = new HookingObject();
                $result->strarray[] = 'ABC';
                return $result;
            },
            function($change,$postfix='ING') {
                $change->addHook('UPDATING_PROPERTY','sarray_changing','strarray');
                unset($change->strarray[0]);
            },'(sarray:NEW: REMOVED:ABC)'],

            [function() { // Test bei Objekt-Felder
                $result = new HookingObject();
                return $result;
            },
            function($change,$postfix='ING') {
                $change->addHook('UPDATING_PROPERTY','field_changing','ofield');
                $dummy = new Dummy();
                $dummy->dummyint = 123;
                $change->ofield = $dummy;
            },'(ofield:NULL=>123)'],
            
            [function() { // Test bei Objekt-Felder
                $result = new HookingObject();
                $dummy = new Dummy();
                $dummy->dummyint = 123;
                $result->ofield = $dummy;
                return $result;
            },            
            function($change,$postfix='ING') {
                $change->addHook('UPDATING_PROPERTY','field_changing','ofield');
                $change->ofield = null;
            },'(ofield:123=>NULL)'],            

            [function() {// Test bei Objekt-Array, Eintrag hinzugef??gt
                $result = new HookingObject();
                $dummy = new Dummy();
                $dummy->dummyint = 123;
                $result->objarray[] = $dummy;
                return $result;
            },
            function($change,$postfix='ING') {
                $change->addHook('UPDATING_PROPERTY','oarray_changing','objarray');
                $dummy = new Dummy();
                $dummy->dummyint = 345;
                $change->objarray[] = $dummy;
            },'(oarray:NEW:345 REMOVED:)'],
            
            [function() { // Test bei String-Array, Eintrag entfernt
                $result = new HookingObject();
                $dummy = new Dummy();
                $dummy->dummyint = 345;
                $result->objarray[] = $dummy;
                return $result;
            },
            function($change,$postfix='ING') {
                $change->addHook('UPDATING_PROPERTY','oarray_changing','objarray');
                unset($change->objarray[0]);
            },'(oarray:NEW: REMOVED:345)'],
            
            [function() { // Test bei Object-Array, Eintrag entfernt
                $result = new HookingObject();
                $dummy1 = new Dummy();
                $dummy1->dummyint = 345;
                $dummy2 = new Dummy();
                $dummy2->dummyint = 456;
                $result->objarray[] = $dummy1;
                $result->objarray[] = $dummy2;
                return $result;
            },
            function($change,$postfix='ING') {
                $change->addHook('UPDATING_PROPERTY','oarray_changing','objarray');
                unset($change->objarray[1]);
            },'(oarray:NEW: REMOVED:456)'],
            
            ];
    }
    
    public function PostHooksProvider() {
        return [
            [function() { // Einfacher Test bei simple-Field
                $result = new HookingObject();
                $result->hooking_int = 222;
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','field_changed','hooking_int');
                $change->hooking_int = 333;
            },'(hooking_int:222=>333)'],
            
            [function() {// Test bei String-Array, Eintrag hinzugef??gt
                $result = new HookingObject();
                $result->strarray[] = 'ABC';
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','sarray_changed','strarray');
                $change->strarray[] = 'DEF';
            },'(sarray:NEW:DEF REMOVED:)'],
            
            [function() { // Test bei String-Array, Eintrag entfernt
                $result = new HookingObject();
                $result->strarray[] = 'ABC';
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','sarray_changed','strarray');
                unset($change->strarray[0]);
            },'(sarray:NEW: REMOVED:ABC)'],
            
            [function() { // Test bei Objekt-Felder
                $result = new HookingObject();
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','field_changed','ofield');
                $dummy = new Dummy();
                $dummy->dummyint = 123;
                $change->ofield = $dummy;
            },'(ofield:NULL=>123)'],
            
            [function() { // Test bei Objekt-Felder
                $result = new HookingObject();
                $dummy = new Dummy();
                $dummy->dummyint = 123;
                $result->ofield = $dummy;
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','field_changed','ofield');
                $change->ofield = null;
            },'(ofield:123=>NULL)'],
            
            [function() {// Test bei Objekt-Array, Eintrag hinzugef??gt
                $result = new HookingObject();
                $dummy = new Dummy();
                $dummy->dummyint = 123;
                $result->objarray[] = $dummy;
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','oarray_changed','objarray');
                $dummy = new Dummy();
                $dummy->dummyint = 345;
                $change->objarray[] = $dummy;
            },'(oarray:NEW:345 REMOVED:)'],
            
            [function() { // Test bei String-Array, Eintrag entfernt
                $result = new HookingObject();
                $dummy = new Dummy();
                $dummy->dummyint = 345;
                $result->objarray[] = $dummy;
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','oarray_changed','objarray');
                unset($change->objarray[0]);
            },'(oarray:NEW: REMOVED:345)'],
            
            [function() { // Test bei Object-Array, Eintrag entfernt
                $result = new HookingObject();
                $dummy1 = new Dummy();
                $dummy1->dummyint = 345;
                $dummy2 = new Dummy();
                $dummy2->dummyint = 456;
                $result->objarray[] = $dummy1;
                $result->objarray[] = $dummy2;
                return $result;
            },
            function($change,$postfix='ED') {
                $change->addHook('UPDAT'.$postfix.'_PROPERTY','oarray_changed','objarray');
                unset($change->objarray[1]);
            },'(oarray:NEW: REMOVED:456)'],
            
            ];
    }
    
    /**
     * @group once
     */
    public function testOnceAgain1() {
        $this->setupHookTables();
        $test = new HookingChild();
        $test::$child_hookstr = '';
        $test->hooking_int = 666;
        $test->childhooking_int = 123;
        $test->commit();
        $test->childhooking_int = 234;
        $test->commit();
        $this->assertEquals('(123=>234)',$test::$child_hookstr);
    }
    
    /**
     * @group once
     */
    public function testOnceAgain2() {
        $this->setupHookTables();
        $test = new HookingChild();
        $test::$child_hookstr = '';
        $dummy1 = new Dummy();
        $dummy1->dummyint = 345;
        $dummy2 = new Dummy();
        $dummy2->dummyint = 456;
        $test->objarray[] = $dummy1;
        $test->objarray[] = $dummy2;
        $test->commit();
        unset($test->objarray[0]);
        $test->commit();
        $this->assertEquals('',$test::$child_hookstr);
    }
    
    /**
     * @group once
     */
    public function testOnceAgain3() {
        $this->setupHookTables();
        $test = new HookingChild();
        $test::$child_hookstr = '';
        $dummy1 = new Dummy();
        $dummy1->dummyint = 345;
        $dummy2 = new Dummy();
        $dummy2->dummyint = 456;
        $test->childhooking_oarray[] = $dummy1;
        $test->childhooking_oarray[] = $dummy2;
        $test->commit();
        unset($test->childhooking_oarray[0]);
        $test->commit();
        $this->assertEquals('(oarray:NEW: REMOVED:345)',$test::$child_hookstr);
    }
    
    private function prepare_object_test() {
        $this->setupHookTables();
        $dummy = new Dummy();
        $dummy->dummyint = 123;
        $dummy->commit();
        $test = new HookingObject();
        $test->addHook('UPDATED_PROPERTY', 'child_changed', 'ofield.dummyint');
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
    
    /**
     * @group externalhooks
     */
    public function testChildChangeObjectIndirect() {
        list($dummy,$test) = $this->prepare_object_test();
       Objects::flushCache();
        // Das folgende ist ein Kunstgriff, weil einen dr??ber der Cache geleert wurde
        Objects::insertCache($test->getID(), $test);
        $readdummy = Objects::load($dummy->getID());
        $readdummy->dummyint = 234;
        $readdummy->commit();
        $this->assertEquals('(Cofield:123->234)',$test->get_hook_str());
        
    }
    
    /**
     * @group externalhooks
     */
    public function testChildChangeObjectBothIndirect() {
        list($dummy,$test) = $this->prepare_object_test();
       Objects::flushCache();
        $readdummy = Objects::load($dummy->getID());
        $readdummy->dummyint = 234;
        $readdummy->commit();
       Objects::flushCache();
        $readtest = Objects::load($test->getID());
        $this->assertEquals('(Cofield:123->234)',$readtest->get_hook_str());
        
    }
    
    private function prepare_array_test() {
        $this->setupHookTables();
        $dummy1 = new Dummy();
        $dummy1->dummyint = 123;
        $dummy2 = new Dummy();
        $dummy2->dummyint = 666;
        $test = new HookingObject();
        $test->addHook('UPDATED_PROPERTY', 'arraychild_changed', 'objarray.dummyint');
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
    
    /**
     * @group externalhooks
     */
    public function testChildChangeArrayIndirect() {
        list($dummy1,$dummy2,$test) = $this->prepare_array_test();
        Objects::flushCache();
        // Das folgende ist ein Kunstgriff, weil einen dr??ber der Cache geleert wurde
        Objects::insertCache($test->getID(), $test);
        $readdummy = Objects::load($dummy1->getID());
        $readdummy->dummyint = 234;
        $readdummy->commit();
        $this->assertEquals('(Cobjarray:123->234)',$test->get_hook_str());        
    }
    
    /**
     * @group externalhooks
     */
    public function testChildChangeArrayBothIndirect() {
        list($dummy1,$dummy2,$test) = $this->prepare_array_test();
        Objects::flushCache();
        $readdummy = Objects::load($dummy1->getID());
        $readdummy->dummyint = 234;
        $readdummy->commit();
        Objects::flushCache();
        $readtest = Objects::load($test->getID());
        $this->assertEquals('(Cobjarray:123->234)',$readtest->get_hook_str());
    }
}
