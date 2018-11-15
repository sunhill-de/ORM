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
                [['NewTag'],['except'],false],
                [['TagA','TagB'],['TagA','TagB'],false]
        ];
    }
    
    /**
     * @dataProvider ChangeTagProvider
     * @group change
     */
    public function testChangeTags($init,$add,$delete,$expect,$changestr) {
        $this->BuildTestClasses();
        $this->clear_system_tables();
        $this->seed();
        $test = new \Sunhill\Test\ts_dummy();        
        for ($i=0;$i<count($init);$i++) {
            $tag = new \Sunhill\Objects\oo_tag($init[$i],true);
            $test->add_tag($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        
        $read =  new \Sunhill\Test\ts_dummy();
        $read->load($test->get_id());
        for ($i=0;$i<count($add);$i++) {
            $tag = new \Sunhill\Objects\oo_tag($add[$i],true);
            $read->add_tag($tag);            
        }
        for ($i=0;$i<count($delete);$i++) {
            $tag = new \Sunhill\Objects\oo_tag($delete[$i],true);
            $read->delete_tag($tag);            
        }
        $read->commit();
        
        $reread =  new \Sunhill\Test\ts_dummy();
        $reread->load($test->get_id());
        
        $given_tags = array();
        for ($i=0;$i<$reread->get_tag_count();$i++) {
            $given_tags[] = $reread->get_tag($i)->get_fullpath();
        }
        sort($expect);
        sort($given_tags);
        $this->assertEquals($expect,$given_tags);
    }
    
    /**
     * @dataProvider ChangeTagProvider
     */
    public function testChangeTagsTrigger($init,$add,$delete,$expect,$changestr) {
        $this->BuildTestClasses();
        $this->clear_system_tables();
        $this->seed();
        $test = new \Sunhill\Test\ts_dummy();
        for ($i=0;$i<count($init);$i++) {
            $tag = new \Sunhill\Objects\oo_tag($init[$i],true);
            $test->add_tag($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        
        $read =  new \Sunhill\Test\ts_dummy();
        $read->load($test->get_id());
        for ($i=0;$i<count($add);$i++) {
            $tag = new \Sunhill\Objects\oo_tag($add[$i],true);
            $read->add_tag($tag);
        }
        for ($i=0;$i<count($delete);$i++) {
            $tag = new \Sunhill\Objects\oo_tag($delete[$i],true);
            $read->delete_tag($tag);
        }
        $read->commit();
        $this->assertEquals($changestr,$read->changestr);                
    }
    
    public function ChangeTagProvider() {
        return [
            [['TagA','TagB'],['TagChildA'],[],['TagA','TagB','TagA.TagChildA'],'ADD:TagA.TagChildA'],
            [['TagA','TagB'],[],['TagA'],['TagB'],'DELETE:TagA'],
            [['TagA','TagB'],['TagChildA'],['TagA'],['TagB','TagA.TagChildA'],'ADD:TagA.TagChildADELETE:TagA']
        ];
    }
}
