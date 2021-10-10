<?php
namespace Sunhill\ORM\Tests\Unit\Operators;

use \Sunhill\ORM\Tests\TestCase;
use \Sunhill\ORM\Operators\ClassOperatorBase;
use \Sunhill\Basic\Utils\Descriptor;
use \Sunhill\ORM\Tests\Objects\ts_dummy;
use \Sunhill\ORM\Tests\Objects\ts_testparent;
use \Sunhill\ORM\Tests\Objects\ts_testchild;

class TestClassOperator extends ClassOperatorBase {

    protected $commands = ['TestA','TestB'];
    
    public $condition = false;
    
    public $flag = '';
    
    protected function cond_something(Descriptor $Descriptor) {
        return $this->condition;
    }
    
    protected function doExecute(Descriptor $Descriptor) {
        $this->flag = 'executed';    
    }
    
    public function set_class(string $class) {
        $this->target_class = $class;
    }
}

class TestClassOperator2 extends ClassOperatorBase {
    
    protected $commands = ['TestA','TestB'];
    
    protected $target_class = ts_dummy::class;

    protected function doExecute(Descriptor $Descriptor) {
    }
    
    
}

class ClassOperatorTest extends TestCase
{
    public function testWithAction() {
        $test = new TestClassOperator();
        $test->condition = true;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_dummy();
        
        $this->assertTrue($test->check($Descriptor));
    }

    public function testWithoutAction() {
        $test = new TestClassOperator();
        $test->condition = true;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestC';
        $Descriptor->object = new ts_dummy();
        
        $this->assertFalse($test->check($Descriptor));
    }

    public function testWithoutCondition() {
        $test = new TestClassOperator();
        $test->condition = false;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_dummy();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testTargetClassPass() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->set_class(ts_dummy::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_dummy();
        
        $this->assertTrue($test->check($Descriptor));        
    }
    
    public function testTargetClassFail() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->set_class(ts_dummy::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_testparent();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testTargetImplicitClassPass() {
        $test = new TestClassOperator2();
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_dummy();
        
        $this->assertTrue($test->check($Descriptor));
    }
    
    public function testTargetImplicitClassFail() {
        $test = new TestClassOperator2();
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_testparent();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testTargetClassChildPass() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->set_class(ts_testparent::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_testchild();
        
        $this->assertTrue($test->check($Descriptor));
    }
    
    public function testTargetClassChildFail() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->set_class(ts_testchild::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_testparent();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testExecution() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->set_class(ts_testparent::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_dummy();
        
        $test->execute($Descriptor);
        $this->assertEquals('executed',$test->flag);
    }
    
}
