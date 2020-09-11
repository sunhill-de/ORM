<?php
namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\oo_object;

class parent_object extends oo_object
{

    protected static $test_array = [
        'A' => '1',
        'B' => '2'
    ];

    protected static $parentonly_array = [
        'R' => '1',
        'S' => '2'
    ];
}

class child_object extends parent_object
{

    protected static $test_array = [
        'C' => '3',
        'D' => '4'
    ];
}

/**
 * Include all tests that should be moved to sunhill-framework in version 2.0
 *
 * @author lokal
 *        
 */
class HirarchicArrayTest extends TestCase
{

    // ======================== Tests for get_hirarchic_array =========================================
    public function testGetHirarchicArray_Parent()
    {
        $this->assertEquals([
            'A' => '1',
            'B' => '2'
        ], parent_object::get_hirarchic_array('test_array'));
    }

    public function testGetHirarchicArray_Child()
    {
        $this->assertEquals([
            'A' => '1',
            'B' => '2',
            'C' => '3',
            'D' => '4'
        ], child_object::get_hirarchic_array('test_array'));
    }

    public function testNotExistingArray()
    {
        $this->expectException(\Sunhill\ORM\SunhillException::class);
        $hilf = child_object::get_hirarchic_array('non_existing');
    }

    public function testParentOnlyHirarchic()
    {
        $this->assertEquals([
            'R' => '1',
            'S' => '2'
        ], child_object::get_hirarchic_array('parentonly_array'));
    }
}
