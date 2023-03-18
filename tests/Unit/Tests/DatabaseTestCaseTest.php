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
}
