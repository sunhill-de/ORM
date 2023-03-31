<?php
namespace Sunhill\ORM\Tests\Feature\Utils;

/**
 *
 * @file UtilObjectListTest.php
 * lang: en
 * dependencies: FilesystemComplexTestCase
 */
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Utils\ObjectList;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Facades\Objects;

class UtilObjectListTest extends DatabaseTestCase
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
        $this->assertEquals('123234123', $result);
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
        $test[] = 9;
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
        $this->assertEquals(Dummy::class, $test->getClass(0));
    }

    public function testGetDistinctClasses()
    {
        $test = $this->get_mixed_test();
        $this->assertEquals([
            Dummy::class,
            TestParent::class
        ], $test->get_distinct_classes());
    }

    protected function get_filter_test()
    {
        $test = new ObjectList();
        $test[] = 1;
        $test[] = 9;
        $test[] = 17;
        $test[] = 25;
        $test[] = 31;
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
        $test->filter_class(TestParent::class, true);
        $this->assertEquals([
            9,17,25
        ], $this->get_id_list($test));
    }

    public function testFilterClass_withoutchildren()
    {
        $test = $this->get_filter_test();
        $test->filter_class(TestParent::class, false);
        $this->assertEquals([
            9,
        ], $this->get_id_list($test));
    }

    public function testRemoveClass_withchildren()
    {
        $test = $this->get_filter_test();
        $test->remove_class(TestParent::class, true);
        $this->assertEquals([1,31], $this->get_id_list($test));
    }

    public function testRemoveClass_withoutchildren()
    {
        $test = $this->get_filter_test();
        $test->remove_class(TestParent::class, false);
        $this->assertEquals([1,17,25,31], $this->get_id_list($test));
    }
}
