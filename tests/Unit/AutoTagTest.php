<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class AutoTagTest extends \Tests\sunhill_testcase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAutotag()
    {
        $this->BuildTestClasses();
        $this->clear_system_tables();
        $test = new \Sunhill\Test\ts_testparent();
        $test->add_auto_tag('gps.Deutschland.Buende.Sonnenhuegel.8');
        $this->assertEquals('autotag.gps.Deutschland.Buende.Sonnenhuegel.8',$test->get_tag(0)->get_fullpath());
    }
}
