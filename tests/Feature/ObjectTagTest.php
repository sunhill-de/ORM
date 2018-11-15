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
        
        for ($i=0;$i<count($set);$i++) {
            if ($expect[$i]=='except') {
                try {
                    $tag = new \Sunhill\Objects\oo_tag($set[$i],$create);
                } catch (\Exception $e) {
                    $this->assertTrue(true);
                    return;
                }
                $this->fail();
            } else {
                $tag = new \Sunhill\Objects\oo_tag($set[$i],$create);
            }
            $test->add_tag($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        for ($i=0;$i<count($expect);$i++) {
            $this->assertEquals($expect[$i],$test->get_tag($i)->get_fullpath());
        }
        $reread =  new \Sunhill\Test\ts_dummy();
        $reread->load($test->get_id());
        for ($i=0;$i<count($expect);$i++) {
            $this->assertEquals($expect[$i],$reread->get_tag($i)->get_fullpath());
        }
    }

    public function TagProvider() {
        return [[['TagA'],['TagA'],false],
                [['TagA.TagChildA'],['TagA.TagChildA'],false],
                [['NewTag'],['NewTag'],true],
                [['NewTag'],['except'],false]
        ];
    }
    
}
