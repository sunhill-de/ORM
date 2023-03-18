<?php
/**
 * @file tests/Unit/Checks/ChecksBaseTest.php
 * Tests the routine in ChecksBase
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Checks\ChecksBase;

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
}