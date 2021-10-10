<?php
namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Objects\ORMObject;

class parent_object extends ORMObject
{

    public static $object_infos = [
        'name'=>'parent_object',            // A repetition of static:$object_name @todo see above
        'table'=>'none',         // A repitition of static:$table_name
        'name_s'=>'parent objects',   // A human readable name in singular
        'name_p'=>'parent objects',  // A human readable name in plural
        'description'=>'Only for hirarchic array tests',
        'options'=>0,               // Reserved for later purposes
    ];
    
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

    public static $object_infos = [
        'name'=>'child_object',            // A repetition of static:$object_name @todo see above
        'table'=>'none',         // A repitition of static:$table_name
        'name_s'=>'child objects',   // A human readable name in singular
        'name_p'=>'child objects',  // A human readable name in plural
        'description'=>'Only for hirarchic array tests',
        'options'=>0,               // Reserved for later purposes
    ];
    
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
        $this->expectException(\Sunhill\ORM\ORMException::class);
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
