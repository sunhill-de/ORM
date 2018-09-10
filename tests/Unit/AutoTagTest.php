<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AutoTagTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAutotag()
    {
        $test = new \Sunhill\Objects\ts_testparent();
        $test->add_auto_tag('gps.Deutschland.Buende.Sonnenhuegel.8');
        $this->assertEquals('autotag.gps.Deutschland.Buende.Sonnenhuegel.8',$test->get_tag(0)->get_fullpath());
    }
}
