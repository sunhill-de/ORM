<?php
namespace Sunhill\ORM\Tests\Unit\Operators;

use \Sunhill\ORM\Tests\TestCase;
use \Sunhill\ORM\Operators\ClassOperatorBase;
use \Sunhill\Basic\Utils\Descriptor;
use \Sunhill\ORM\Tests\Objects\Dummy;
use \Sunhill\ORM\Tests\Objects\TestParent;
use \Sunhill\ORM\Tests\Objects\TestChild;

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
    
    public function setClass(string $class) {
        $this->target_class = $class;
    }
}

class TestClassOperator2 extends ClassOperatorBase {
    
    protected $commands = ['TestA','TestB'];
    
    protected $target_class = Dummy::class;

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
        $Descriptor->object = new Dummy();
        
        $this->assertTrue($test->check($Descriptor));
    }

    public function testWithoutAction() {
        $test = new TestClassOperator();
        $test->condition = true;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestC';
        $Descriptor->object = new Dummy();
        
        $this->assertFalse($test->check($Descriptor));
    }

    public function testWithoutCondition() {
        $test = new TestClassOperator();
        $test->condition = false;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new Dummy();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testTargetClassPass() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->setClass(Dummy::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new Dummy();
        
        $this->assertTrue($test->check($Descriptor));        
    }
    
    public function testTargetClassFail() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->setClass(Dummy::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new TestParent();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testTargetImplicitClassPass() {
        $test = new TestClassOperator2();
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new Dummy();
        
        $this->assertTrue($test->check($Descriptor));
    }
    
    public function testTargetImplicitClassFail() {
        $test = new TestClassOperator2();
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new TestParent();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testTargetClassChildPass() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->setClass(TestParent::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new TestChild();
        
        $this->assertTrue($test->check($Descriptor));
    }
    
    public function testTargetClassChildFail() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->setClass(TestChild::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new TestParent();
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testExecution() {
        $test = new TestClassOperator();
        $test->condition = true;
        $test->setClass(TestParent::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new Dummy();
        
        $test->execute($Descriptor);
        $this->assertEquals('executed',$test->flag);
    }
    
}
