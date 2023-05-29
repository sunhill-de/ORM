<?php
/**
 * Some essential selftests for the testing environment
 * @file tests/Unit/Tests/DatabaseTestCaseTest.php
 * Tests the routine in ChecksBase
 */
namespace Sunhill\ORM\Tests\Unit\Tests;

use Illuminate\Support\Facades\Schema;
use Sunhill\ORM\Tests\DatabaseTestCase;

class DatabaseTestCaseTest extends DatabaseTestCase
{
    
    /**
     * Tests if the necessary tables exists
     * @dataProvider tableExistProvider
     * @param string $table
     */
    public function testTableExist(string $table)
    {
        $this->assertTrue(Schema::hasTable($table));
    }
    
    public function tableExistProvider()
    {
        return [
           // Core tables
            ['objects'],
            ['attributes'],
            ['attributeobjectassigns'],
            ['tagcache'],
            ['tags'],
            ['tagobjectassigns'],
           
           // Test tables 
            ['calcclasses'],
            ['dummies'],
            ['dummychildren'],
            ['referenceonlies'],
            ['testparents'],
            ['testchildren'],
            ['testsimplechildren'],
            ['thirdlevelchildren'],
            
            ['attr_attribute1'],
            ['attr_attribute2'],
            ['attr_char_attribute'],
            ['attr_child_attribute'],
            ['attr_float_attribute'],
            ['attr_general_attribute'],
            ['attr_int_attribute'],
            ['attr_text_attribute'],
        ];
    }
    
    /**
     * @dataProvider ValueProvider
     * @param string $table
     * @param string $field
     * @param unknown $expect
     */
    public function testValue(string $table, string $field, $expect)
    {
        $this->assertDatabaseHas($table,[$field=>$expect]);
    }
    
    public function ValueProvider()
    {
        return [
            ['dummies','dummyint',123],
            ['testparents','parentchar','ARG'],
            ['testparents','parentchar',null],
            ['objects','id',30],
            ['referenceonlies','id',30]
        ];    
    }
    
}
