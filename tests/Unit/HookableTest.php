<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;

class testhookable extends \Sunhill\hookable {

    public $parent;
    
    public $flag = '';
    
    protected function setup_hooks() {
        $this->add_hook('hook1','call_hook1');
        $this->add_hook('hook2','call_hook2','test');
        $this->add_hook('hook3','call_hook3','test');
    }
    
    public function hooked_method1() {
        $this->check_for_hook('hook1');
    }
    
    public function hooked_method2(string $param) {
        $this->check_for_hook('hook2',$param,array($param));        
    }
    
    public function hooked_method3(string $param) {
        $this->check_for_hook('hook3',$param,array($param));
    }
    
    protected function call_hook1() {
        $this->flag = 'flag';
    }
    
    protected function call_hook2($params) {
        $this->flag = $params[0];
    }
    
    protected function call_hook3($params) {
        $this->flag = $params[0];        
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
            $test->add_hook('hook3','call_flag','test',$this);
            $test->hooked_method3('test');
            $this->assertEquals('test',$test->flag);
            $this->assertEquals('flag',$this->flag);
        }
}
