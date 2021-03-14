<?php
namespace Sunhill\ORM\Tests\Unit;

use \Sunhill\ORM\Tests\TestCase;
use \Sunhill\ORM\Operators\OperatorBase;
use \Sunhill\Basic\Utils\descriptor;

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
}

class OperatorTest extends TestCase
{
    public function testWithAction() {
        $test = new TestOperator();
        $test->condition = true;
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $this->assertTrue($test->check($descriptor));
    }

    public function testWithoutAction() {
        $test = new TestOperator();
        $test->condition = true;
        $descriptor = new descriptor();
        $descriptor->command = 'TestC';
        $this->assertFalse($test->check($descriptor));
    }

    public function testWithoutCondition() {
        $test = new TestOperator();
        $test->condition = false;
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $this->assertFalse($test->check($descriptor));
    }
    
    public function testExecution() {
        $test = new TestOperator();
        $test->condition = true;
        $descriptor = new descriptor();
        $descriptor->command = 'TestA';
        $test->execute($descriptor);
        $this->assertEquals('executed',$test->flag);
    }
}
