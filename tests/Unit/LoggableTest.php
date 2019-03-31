<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;

class LoggableTest extends TestCase
{

        public function testSetLoglevel() {
            $test = new \Sunhill\loggable();
            $test->set_loglevel(LL_DEBUG);
            $this->assertEquals(LL_DEBUG,$test->get_loglevel());
        }
        
}
