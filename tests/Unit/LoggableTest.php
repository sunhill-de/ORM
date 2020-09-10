<?php
/**
 * @file LoggableTest.php
 * Unittest for the class loggable
 * @todo obsolete test for a obsolete class?
 * Lang en
 * Reviewstate: 2020-08-10
 */

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Sunhill\ORM\loggable;

class LoggableTest extends TestCase
{

        public function testSetLoglevel() {
            $test = new \Sunhill\ORM\loggable();
            $test->set_loglevel(LL_DEBUG);
            $this->assertEquals(LL_DEBUG,$test->get_loglevel());
        }
        
}
