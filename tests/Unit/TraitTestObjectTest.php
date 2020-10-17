<?php

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Traits\TestObject;
use Sunhill\ORM\SunhillException;
use Sunhill\ORM\Test\ts_dummy;
use Sunhill\ORM\Test\ts_testparent;
use Sunhill\ORM\Test\ts_testchild;

class test_trait_class {
    
    use TestObject;
    
    public function test($test,$allowed) {
        return $this->is_valid_object($test,$allowed);
    }
}

class TraitTestObjectTest extends TestCase
{

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
            [function() { return new ts_dummy(); },'dummy',true],
            [function() { return new ts_dummy(); },'Sunhill\ORM\Test\ts_dummy',true],
            [function() { return new ts_dummy(); },'testparent',false],
            [function() { return new ts_testchild(); },'testparent',true],
            [function() { return new ts_testchild(); },['testparent'],true],
            [function() { return new ts_testchild(); },['dummy','testparent'],true],
            ];
    }
}
