<?php
/**
 *
 * @file HookableTest.php
 * Unittest for the class Hookable
 * Lang en
 * Reviewstate: 2020-08-10
 */

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Hookable;

class testhookable extends Hookable {

    public $parent;
    
    public $flag = '';
    
    public $params='';
    
    protected function setupHooks() {
        $this->addHook('hook1','call_hook1');
        $this->addHook('hook2','call_hook2','test');
        $this->addHook('hook3','call_hook3','test');
    }
    
    public function hooked_method1() {
        $this->checkForHook('hook1');
    }
    
    public function hooked_method2(string $param) {
        $this->checkForHook('hook2',$param,array($param));        
    }
    
    public function hooked_method3(string $param) {
        $this->checkForHook('hook3',$param,array($param));
    }
    
    protected function call_hook1() {
        $this->flag = 'flag';
    }
    
    protected function call_hook2($params) {
        $this->flag = $params[0];
    }
    
    protected function call_hook3($params) {
        $this->flag = $params[0];
        $this->params = 'action='.$params['action'].",subaction=".$params['subaction'];
    }
}

class HookableTest extends TestCase
{
        public $flag = '';
        
        public function call_flag() {
            $this->flag = 'flag';    
        }
        
        public function testSimpleHook() {
            $test = new testhookable();
            $test->hooked_method1();
            $this->assertEquals('flag',$test->flag);
        }
        
        public function testHookWithSecondaryPass() {
            $test = new testhookable();
            $test->hooked_method2('test');
            $this->assertEquals('test',$test->flag);            
        }
        
        public function testHookWithSecondaryFail() {
            $test = new testhookable();
            $test->hooked_method2('somethingelse');
            $this->assertEquals('',$test->flag);            
        }
        
        public function testDoubleHook() {
            $test = new testhookable();
            $test->addHook('hook3','call_flag','test',$this);
            $test->hooked_method3('test');
            $this->assertEquals('test',$test->flag);
            $this->assertEquals('flag',$this->flag);
            $this->assertEquals('action=hook3,subaction=test',$test->params);
        }
}
