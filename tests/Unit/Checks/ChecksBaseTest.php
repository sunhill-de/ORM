<?php
/**
 * @file tests/Unit/Checks/ChecksBaseTest.php
 * Tests the routine in ChecksBase
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Checks\ChecksBase;
use Sunhill\Basic\Facades\Checks;

class ChecksBaseTest extends DatabaseTestCase
{

    /**
     * Tests: checkForDanglingPointers
     */
    public function testCheckForDanglingPointers()
    {
        $test = new ChecksBase();
        $this->assertTrue(empty($this->callProtectedMethod($test,'checkForDanglingPointers',
        ['tags','parent_id','tags','id']
            )));
        DB::table('tags')->where('id',2)->delete();
        $this->assertFalse(empty($this->callProtectedMethod($test,'checkForDanglingPointers',
            ['tags','parent_id','tags','id']
            )));
    }
    
    /**
     * Tests: repairDanglingPointers
     */
    public function testRepairDanglingPointers()
    {
        $test = new ChecksBase();
        DB::table('tags')->where('id',2)->delete();
        $this->callProtectedMethod($test,'repairDanglingPointers',
            ['tags','parent_id','tags','id']
            );
        $this->assertTrue(empty($this->callProtectedMethod($test,'checkForDanglingPointers',
            ['tags','parent_id','tags','id']
            )));
    }
    
    public function testRunAllTests()
    {
        Checks::check(false);
        $this->assertEquals(0,Checks::getTestsFailed());
    }
}