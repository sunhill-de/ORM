<?php
namespace Sunhill\ORM\Tests\Unit;

use \Sunhill\ORM\Tests\TestCase;
use \Sunhill\ORM\Operators\OperatorBase;
use \Sunhill\Basic\Utils\descriptor;
use \Sunhill\ORM\Tests\Objects\ts_dummy;
use \Sunhill\ORM\Tests\Objects\ts_testparent;
use \Sunhill\ORM\Tests\Objects\ts_testchild;

class TestOperator extends OperatorBase {

    protected $commands = ['TestA','TestB'];
    
    public $condition = false;
    
    public $flag = '';
    
    protected function cond_something(descriptor $descriptor) {
        return $this->condition;
    }
    
    protected function do_execute(descriptor $descriptor) {
        $this->flag = 'executed';    
    }
    
    public function set_class(string $class) {
        $this->target_class = $class;
    }
}

class TestOperator2 extends OperatorBase {
    
    protected $commands = ['TestA','TestB'];
    
    protected $target_class = ts_dummy::class;

    protected function do_execute(descriptor $descriptor) {
    }
    
    
}

class OperatorTest extends TestCase
{
    public function testWithAction() {
        $test = new TestOperator();
        $test->condition = true;
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_dummy();
        
        $this->assertTrue($test->check($descriptor));
    }

    public function testWithoutAction() {
        $test = new TestOperator();
        $test->condition = true;
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestC';
        $descriptor->object = new ts_dummy();
        
        $this->assertFalse($test->check($descriptor));
    }

    public function testWithoutCondition() {
        $test = new TestOperator();
        $test->condition = false;
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_dummy();
        
        $this->assertFalse($test->check($descriptor));
    }
    
    public function testTargetClassPass() {
        $test = new TestOperator();
        $test->condition = true;
        $test->set_class(ts_dummy::class);
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_dummy();
        
        $this->assertTrue($test->check($descriptor));        
    }
    
    public function testTargetClassFail() {
        $test = new TestOperator();
        $test->condition = true;
        $test->set_class(ts_dummy::class);
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_testparent();
        
        $this->assertFalse($test->check($descriptor));
    }
    
    public function testTargetImplicitClassPass() {
        $test = new TestOperator2();
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_dummy();
        
        $this->assertTrue($test->check($descriptor));
    }
    
    public function testTargetImplicitClassFail() {
        $test = new TestOperator2();
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_testparent();
        
        $this->assertFalse($test->check($descriptor));
    }
    
    public function testTargetClassChildPass() {
        $test = new TestOperator();
        $test->condition = true;
        $test->set_class(ts_testparent::class);
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_testchild();
        
        $this->assertTrue($test->check($descriptor));
    }
    
    public function testTargetClassChildFail() {
        $test = new TestOperator();
        $test->condition = true;
        $test->set_class(ts_testchild::class);
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_testparent();
        
        $this->assertFalse($test->check($descriptor));
    }
    
    public function testExecution() {
        $test = new TestOperator();
        $test->condition = true;
        $test->set_class(ts_testparent::class);
        
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $descriptor->object = new ts_dummy();
        
        $test->execute($descriptor);
        $this->assertEquals('executed',$test->flag);
    }
    
}
