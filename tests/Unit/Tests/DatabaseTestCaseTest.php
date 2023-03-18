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
            ['attributevalues'],
            ['caching'],
            ['externalhooks'],
            ['objectobjectassigns'],
            ['stringobjectassigns'],
            ['tagcache'],
            ['tags'],
            ['tagobjectassigns'],
           
           // Test tables 
            ['dummies'],
            ['dummychildren'],
            ['testparents'],
            ['testchildren'],
            ['testsimplechildren']
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
