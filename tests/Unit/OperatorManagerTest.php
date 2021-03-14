<?php
namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Managers\operator_manager;
use Sunhill\ORM\Operators\OperatorBase;
use Sunhill\Basic\Utils\descriptor;
use Sunhill\ORM\Facades\Operators;

class TestOperator1 extends OperatorBase {
    
    protected $commands = ['test1'];
    
    protected function do_execute(descriptor $descriptor) {
        
    }
}

class TestOperator2 extends OperatorBase {
    
    protected $commands = ['test2'];
    
    protected function do_execute(descriptor $descriptor) {
        
    }
}

class TestOperator3 extends OperatorBase {
    
    protected $commands = ['test1','test2'];
    
    protected function do_execute(descriptor $descriptor) {
        
    }
}

class OperatorTest extends TestCase
{
    
    public function testOperatorManager() {
        $manager = new operator_manager();
        $manager
            ->add_operator(TestOperator1::class)
            ->add_operator(TestOperator2::class)
            ->add_operator(TestOperator3::class);
        $this->assertEquals(3,$manager->get_operator_count());
    }
    
    public function testOperatorFacade() {
        Operators::flush();
        Operators::add_operator(TestOperator1::class)
        ->add_operator(TestOperator2::class)
        ->add_operator(TestOperator3::class);
        $this->assertEquals(3,Operators::get_operator_count());
    }
}
