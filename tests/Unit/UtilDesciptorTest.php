<?php
namespace Sunhill\ORM\Tests\Unit;

/**
 *
 * @file UtilDescriptorTest.php
 * lang: en
 * dependencies: FilemanagerTestCase
 */
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Utils\descriptor;
use Sunhill\ORM\Utils\DescriptorException;

class test_descriptor extends descriptor {
    
    protected $autoadd = false;
    
    public $flag = '';
    
    protected function setup_fields() {
        $this->test = 'ABC';
        $this->test2 = 'DEF';
    }
    
    protected function test_changing(descriptor $diff) {
        if ($diff->from == 'ABC') {
            return true;
        } else {
            return false;
        }
    }
    
    protected function test_changed(descriptor $diff) {
        $this->flag = $diff->from."=>".$diff->to;    
    }
}

class UtilDescriptorTest extends TestCase
{

    public function testSetGet()
    {
        $test = new descriptor();
        $test->test = 'ABC';
        $this->assertEquals('ABC', $test->test);
    }

    public function testNotSet()
    {
        $test = new descriptor();
        $this->assertTrue($test->notset->empty());
    }

    public function testEmpty()
    {
        $test = new descriptor();
        $this->assertTrue($test->empty());
        $this->assertFalse($test->has_error());
    }

    public function testError()
    {
        $test = new descriptor();
        $test->set_error('There was an error');
        $this->assertEquals('There was an error', $test->has_error());
    }

    /**
     *
     * @group double
     */
    public function testDoubleDescriptor()
    {
        $test = new descriptor();
        $test->test1 = 'ABC';
        $test->test2->test = 'ABC';
        $this->assertEquals($test->test1, $test->test2->test);
    }

    public function testForeach()
    {
        $test = new descriptor();
        $test->test1 = 'ABC';
        $test->test2 = 'BCE';
        $test->anothertest = 123;
        $result = '';
        foreach ($test as $key => $value) {
            $result .= $key . '=>' . $value;
        }
        $this->assertEquals('test1=>ABCtest2=>BCEanothertest=>123', $result);
    }

    public function testGet()
    {
        $test = new descriptor();
        $test->test1 = 'ABC';
        $this->assertEquals('ABC', $test->get_test1());
    }

    public function testSet()
    {
        $test = new descriptor();
        $test->set_test1('ABC');
        $this->assertEquals('ABC', $test->test1);
    }

    public function testCascadingSet()
    {
        $test = new descriptor();
        $test->set_test1('ABC')->set_test2('DEF');
        $this->assertEquals('DEF', $test->test2);
    }

    public function testRaiseException()
    {
        $this->expectException(DescriptorException::class);
        $test = new descriptor();
        $test->not_existing_function();
    }
    
    public function testNoAutoadd() {
        $this->expectException(DescriptorException::class);
        $test = new test_descriptor();
        $test->test3 = 'ABC';
    }
    
    public function testPassChange() {
        $test = new test_descriptor();
        $test->test = 'CBA';
        $this->assertEquals('CBA',$test->test);
        return $test;
    }
    
    /**
     * @depends testPassChange
     */
    public function testFailChange(test_descriptor $test) {
        $this->expectException(DescriptorException::class);
        $test->test = 'ZZZ';
    }

    /**
     * @depends testPassChange
     */
    public function testPostTrigger(test_descriptor $test) {
        $this->assertEquals('ABC=>CBA',$test->flag);
    }
    
    public function testHasKey() {
        $test = new descriptor();
        $test->abc = 'abc';
        $this->assertTrue($test->is_defined('abc'));
        $this->assertFalse($test->is_defined('notdefined'));
    }
}
