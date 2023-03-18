<?php
/**
 * @file tests/Unit/Checks/ChecksBaseTest.php
 * Tests the routine in ChecksBase
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Checks\ChecksBase;

class ChecksBaseTest extends DatabaseTestCase
{

    /**
     * Tests checkForDanglingPointers
     */
    public function testCheckForDanglingPointers()
    {
        $test = new ChecksBase();
        
        
    }
}