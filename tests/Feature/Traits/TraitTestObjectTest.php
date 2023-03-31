<?php

namespace Sunhill\ORM\Tests\Feature\Traits;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Traits\TestObject;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Facades\Classes;

class test_trait_class {
    
    use TestObject;
    
    public function test($test,$allowed) {
        return $this->isValidObject($test,$allowed);
    }
}

class TraitTestObjectTest extends TestCase
{

    public function setUp() : void {
        parent::setUp();
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
    }
    
    /**
     * @dataProvider IsValidProvider
     * @param unknown $test
     * @param unknown $allowed
     * @param unknown $expect
     */
    public function testIsValid($test,$allowed,$expect) {
        $test = $test();
        $class = new test_trait_class();
        if ($expect === 'except') {
            $this->expectException(\Exception::class);
        }
        $this->assertEquals($expect,$class->test($test,$allowed));
    }
    
    public function IsValidProvider() {
        return [
            [function() { return new Dummy(); },'dummy',true],
            [function() { return new Dummy(); },'Sunhill\ORM\Tests\Testobjects\Dummy',true],
            [function() { return new Dummy(); },'testparent',false],
            [function() { return new TestChild(); },'testparent',true],
            [function() { return new TestChild(); },['testparent'],true],
            [function() { return new TestChild(); },['dummy','testparent'],true],
            ];
    }
}
