<?php
namespace Sunhill\ORM\Tests\Unit\Operators;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Managers\OperatorManager;
use Sunhill\ORM\Operators\OperatorBase;
use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Facades\Operators;
use Sunhill\ORM\Tests\Testobjects\Dummy;

class ManagerOperator1 extends OperatorBase {
    
    protected $commands = ['test1'];
    
    protected $target_class = Dummy::class;
    
    protected $prio = 3;
    
    protected function doExecute(Descriptor $Descriptor) {
        $Descriptor->object->dummyint++;
        $Descriptor->payload = 4;
    }
}

class ManagerOperator2 extends OperatorBase {
    
    protected $commands = ['test2'];

    protected $target_class = Dummy::class;
    
    protected $prio = 2;
    
    protected function doExecute(Descriptor $Descriptor) {
        $Descriptor->object->dummyint+=2;        
    }
}

class ManagerOperator3 extends OperatorBase {
    
    protected $commands = ['test1','test2'];

    protected $target_class = Dummy::class;
    
    protected $prio = 1;
    
    protected function doExecute(Descriptor $Descriptor) {
        $Descriptor->object->dummyint*=3;        
    }
}

class OperatorManagerTest extends TestCase
{
    
    public function testOperatorManager() {
        $manager = new OperatorManager();
        $manager
        ->addOperator(ManagerOperator1::class)
        ->addOperator(ManagerOperator2::class)
        ->addOperator(ManagerOperator3::class);
        $this->assertEquals(3,$manager->getOperatorCount());
    }
    
    public function testOperatorFacade() {
        Operators::flush();
        Operators::addOperator(ManagerOperator1::class)
        ->addOperator(ManagerOperator2::class)
        ->addOperator(ManagerOperator3::class);
        $this->assertEquals(3,Operators::getOperatorCount());
    }
    
    public function testOperatorChain() {
        Operators::flush();
        Operators::addOperator(ManagerOperator1::class)
        ->addOperator(ManagerOperator2::class)
        ->addOperator(ManagerOperator3::class);
        
        $test = new Dummy();
        $test->dummyint = 1;
        
        Operators::ExecuteOperators('test1',$test);
        $this->assertEquals(4,$test->dummyint);
    }
    
    
    public function testOperatorChainWithDescriptor() {
        Operators::flush();
        Operators::addOperator(ManagerOperator1::class)
        ->addOperator(ManagerOperator2::class)
        ->addOperator(ManagerOperator3::class);
        
        $test = new Dummy();
        $test->dummyint = 1;
        
        $Descriptor = new Descriptor();
        $Descriptor->payload = 3;
        
        Operators::ExecuteOperators('test1',$test,$Descriptor);
        $this->assertEquals($Descriptor->payload,$test->dummyint);
    }
    
}
