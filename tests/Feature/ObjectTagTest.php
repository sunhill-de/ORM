<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectTagTest extends ObjectCommon
{
    
    /**
     * @dataProvider TagProvider
     */
    public function testTagObject($set,$expect,$create) {
        $this->BuildTestClasses();
        $this->clear_system_tables();
        $this->seed();
        $test = new \Sunhill\Test\ts_dummy();
        if ($expect=='except') {
            try {
                $tag = new \Sunhill\Objects\oo_tag($set,$create);
            } catch (\Exception $e) {
                $this->assertTrue(true);
                return;
            }
            $this->fail();
        } else {
            $tag = new \Sunhill\Objects\oo_tag($set,$create);
        }
        $test->add_tag($tag);
        $test->dummyint = 1;
        $test->commit();
        $this->assertEquals($expect,$test->get_tag(0)->get_fullpath());
    }

    public function TagProvider() {
        return [['TagA','TagA',false],
                ['TagA.TagChildA','TagA.TagChildA',false],
                ['NewTag','NewTag',true],
                ['NewTag','except',false]
        ];
    }
    
}
