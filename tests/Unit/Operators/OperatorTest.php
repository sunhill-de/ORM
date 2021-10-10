<?php
namespace Sunhill\ORM\Tests\Unit\Operators;

use \Sunhill\ORM\Tests\TestCase;
use \Sunhill\ORM\Operators\OperatorBase;
use \Sunhill\Basic\Utils\Descriptor;
use \Sunhill\ORM\Tests\Objects\ts_dummy;
use \Sunhill\ORM\Tests\Objects\ts_testparent;
use \Sunhill\ORM\Tests\Objects\ts_testchild;

class TestOperator extends OperatorBase {

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

class OperatorTest extends TestCase
{
    public function testWithAction() {
        $test = new TestOperator();
        $test->condition = true;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        
        $this->assertTrue($test->check($Descriptor));
    }

    public function testWithoutAction() {
        $test = new TestOperator();
        $test->condition = true;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestC';
        
        $this->assertFalse($test->check($Descriptor));
    }

    public function testWithoutCondition() {
        $test = new TestOperator();
        $test->condition = false;
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        
        $this->assertFalse($test->check($Descriptor));
    }
    
    public function testExecution() {
        $test = new TestOperator();
        $test->condition = true;
        $test->set_class(ts_testparent::class);
        
        $Descriptor = new Descriptor();
        $Descriptor->command = 'TestA';
        $Descriptor->object = new ts_dummy();
        
        $test->execute($Descriptor);
        $this->assertEquals('executed',$test->flag);
    }
    
}
