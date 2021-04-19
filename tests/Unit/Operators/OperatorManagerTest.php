<?php
namespace Sunhill\ORM\Tests\Unit\Operators;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Managers\operator_manager;
use Sunhill\ORM\Operators\OperatorBase;
use Sunhill\Basic\Utils\descriptor;
use Sunhill\ORM\Facades\Operators;
use Sunhill\ORM\Tests\Objects\ts_dummy;

class ManagerOperator1 extends OperatorBase {
    
    protected $commands = ['test1'];
    
    protected $target_class = ts_dummy::class;
    
    protected $prio = 3;
    
    protected function do_execute(descriptor $descriptor) {
        $descriptor->object->dummyint++;
        $descriptor->payload = 4;
    }
}

class ManagerOperator2 extends OperatorBase {
    
    protected $commands = ['test2'];

    protected $target_class = ts_dummy::class;
    
    protected $prio = 2;
    
    protected function do_execute(descriptor $descriptor) {
        $descriptor->object->dummyint+=2;        
    }
}

class ManagerOperator3 extends OperatorBase {
    
    protected $commands = ['test1','test2'];

    protected $target_class = ts_dummy::class;
    
    protected $prio = 1;
    
    protected function do_execute(descriptor $descriptor) {
        $descriptor->object->dummyint*=3;        
    }
}

class OperatorManagerTest extends TestCase
{
    
    public function testOperatorManager() {
        $manager = new operator_manager();
        $manager
        ->add_operator(ManagerOperator1::class)
        ->add_operator(ManagerOperator2::class)
        ->add_operator(ManagerOperator3::class);
        $this->assertEquals(3,$manager->get_operator_count());
    }
    
    public function testOperatorFacade() {
        Operators::flush();
        Operators::add_operator(ManagerOperator1::class)
        ->add_operator(ManagerOperator2::class)
        ->add_operator(ManagerOperator3::class);
        $this->assertEquals(3,Operators::get_operator_count());
    }
    
    public function testOperatorChain() {
        Operators::flush();
        Operators::add_operator(ManagerOperator1::class)
        ->add_operator(ManagerOperator2::class)
        ->add_operator(ManagerOperator3::class);
        
        $test = new ts_dummy();
        $test->dummyint = 1;
        
        Operators::ExecuteOperators('test1',$test);
        $this->assertEquals(4,$test->dummyint);
    }
    
    
    public function testOperatorChainWithDescriptor() {
        Operators::flush();
        Operators::add_operator(ManagerOperator1::class)
        ->add_operator(ManagerOperator2::class)
        ->add_operator(ManagerOperator3::class);
        
        $test = new ts_dummy();
        $test->dummyint = 1;
        
        $descriptor = new descriptor();
        $descriptor->payload = 3;
        
        Operators::ExecuteOperators('test1',$test,$descriptor);
        $this->assertEquals($descriptor->payload,$test->dummyint);
    }
    
}
