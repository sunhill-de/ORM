<?php
namespace Sunhill\ORM\Tests\Unit\Utils;

/**
 *
 * @file UtilObjectListTest.php
 * lang: en
 * dependencies: FilesystemComplexTestCase
 */
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Utils\ObjectList;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Facades\Objects;

class UtilObjectListTest extends DBTestCase
{

    public function testAddObject_method_count()
    {
        $test = new ObjectList();
        $test->add(1);
        $test->add(2);
        $test->add(3);
        $this->assertEquals(3, $test->count());
        return $test;
    }

    /**
     *
     * @depends testAddObject_method_count
     */
    public function testAddObject_method_countdirectt($test)
    {
        $this->assertEquals(3, count($test));
        return $test;
    }

    /**
     *
     * @depends testAddObject_method_count
     */
    public function testAddObject_method_getID($test)
    {
        $this->assertEquals(2, $test->getID(1));
        return $test;
    }

    /**
     *
     * @depends testAddObject_method_count
     */
    public function testAddObject_method_get($test)
    {
        $this->assertEquals(234, $test->get(1)->dummyint);
        return $test;
    }

    /**
     *
     * @depends testAddObject_method_count
     */
    public function testAddObject_method_array($test)
    {
        $this->assertEquals(234, $test[1]->dummyint);
        return $test;
    }

    /**
     *
     * @depends testAddObject_method_count
     */
    public function testAddObject_method_foreach($test)
    {
        $result = '';
        foreach ($test as $item) {
            $result .= $item->dummyint;
        }
        $this->assertEquals('123234345', $result);
        return $test;
    }

    public function testAddObject_array()
    {
        $test = new ObjectList();
        $test[] = 1;
        $test[] = 2;
        $test[] = 3;
        $this->assertEquals(3, $test->count());
        return $test;
    }

    public function testAddObject_array_mixed()
    {
        $test = new ObjectList();
        $dummy = Objects::load(1);
        $test[] = $dummy;
        $test[] = 2;
        $test[] = 3;
        $this->assertEquals(3, $test->count());
        return $test;
    }

    public function testEmpty()
    {
        $test = new ObjectList();
        $before = $test->empty();
        $test->add(1);
        $this->assertEquals($test->empty(), ! $before);
    }

    protected function get_mixed_test()
    {
        $test = new ObjectList();
        $test[] = 1;
        $test[] = 5;
        $test[] = 2;
        return $test;
    }

    public function testAddMixedClasses()
    {
        $test = $this->get_mixed_test();
        $this->assertEquals(123, $test[0]->dummyint);
        $this->assertEquals('ABC', $test[1]->parentchar);
    }

    public function testGetClass()
    {
        $test = $this->get_mixed_test();
        $this->assertEquals('Sunhill\ORM\Tests\Objects\ts_dummy', $test->getClass(0));
    }

    public function testGetDistinctClasses()
    {
        $test = $this->get_mixed_test();
        $this->assertEquals([
            'Sunhill\ORM\Tests\Objects\ts_dummy',
            'Sunhill\ORM\Tests\Objects\ts_testparent'
        ], $test->get_distinct_classes());
    }

    protected function get_filter_test()
    {
        $test = new ObjectList();
        $test[] = 1;
        $test[] = 5;
        $test[] = 6;
        $test[] = 2;
        $test[] = 7;
        return $test;
    }

    protected function get_id_list($test)
    {
        $result = [];
        for ($i = 0; $i < count($test); $i ++) {
            $result[] = $test->getID($i);
        }
        return $result;
    }

    public function testFilterClass_withchildren()
    {
        $test = $this->get_filter_test();
        $test->filter_class('\Sunhill\ORM\Tests\Objects\ts_testparent', true);
        $this->assertEquals([
            5,6,7
        ], $this->get_id_list($test));
    }

    public function testFilterClass_withoutchildren()
    {
        $test = $this->get_filter_test();
        $test->filter_class('\Sunhill\ORM\Tests\Objects\ts_testparent', false);
        $this->assertEquals([
            5,
        ], $this->get_id_list($test));
    }

    public function testRemoveClass_withchildren()
    {
        $test = $this->get_filter_test();
        $test->remove_class('\Sunhill\ORM\Tests\Objects\ts_testparent', true);
        $this->assertEquals([1,2], $this->get_id_list($test));
    }

    public function testRemoveClass_withoutchildren()
    {
        $test = $this->get_filter_test();
        $test->remove_class('\Sunhill\ORM\Tests\Objects\ts_testparent', false);
        $this->assertEquals([1,6,2,7], $this->get_id_list($test));
    }
}
