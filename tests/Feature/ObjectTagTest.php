<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Objects\oo_tag;
use Sunhill\ORM\Test\ts_dummy;
use Sunhill\ORM\Tests\DBTestCase;

class ObjectTagTest extends DBTestCase
{
    
    /**
     * @dataProvider TagProvider
     */
    public function testTagObject($set,$expect,$create) {
         $test = new ts_dummy();
        
        for ($i=0;$i<count($set);$i++) {
            if ($expect[$i]=='except') {
                try {
                    $tag = new oo_tag($set[$i],$create);
                } catch (\Exception $e) {
                    $this->assertTrue(true);
                    return;
                }
                $this->fail();
            } else {
                $tag = new oo_tag($set[$i],$create);
            }
            $test->tags->stick($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        for ($i=0;$i<count($expect);$i++) {
            $this->assertEquals($expect[$i],$test->tags[$i]);
        }
        $reread =  new ts_dummy();
        oo_object::flush_cache();
        $reread = oo_object::load_object_of($test->get_id());
        for ($i=0;$i<count($expect);$i++) {
            $this->assertEquals($expect[$i],$reread->tags[$i]);
        }
    }

    public function TagProvider() {
        return [[['TagA'],['TagA'],false],
                [['TagB.TagC'],['TagB.TagC'],false],
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
        $test = new ts_dummy();        
        for ($i=0;$i<count($init);$i++) {
            $tag = new oo_tag($init[$i],true);
            $test->tags->stick($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        
        oo_object::flush_cache();
        $read =  new ts_dummy();
        $read = oo_object::load_object_of($test->get_id());
        for ($i=0;$i<count($add);$i++) {
            $tag = new oo_tag($add[$i],true);
            $read->tags->stick($tag);            
        }
        for ($i=0;$i<count($delete);$i++) {
            $tag = new oo_tag($delete[$i],true);
            $read->tags->remove($tag);            
        }
        $read->commit();
        
        oo_object::flush_cache();
        $reread = oo_object::load_object_of($test->get_id());
        
        $given_tags = array();
        for ($i=0;$i<count($reread->tags);$i++) {
            $given_tags[] = $reread->tags[$i];
        }
        sort($expect);
        sort($given_tags);
        $this->assertEquals($expect,$given_tags);
    }
    
    /**
     * @dataProvider ChangeTagProvider
     * @group Trigger
     */
    public function testChangeTagsTrigger($init,$add,$delete,$expect,$changestr) {
        $test = new ts_dummy();
        for ($i=0;$i<count($init);$i++) {
            $tag = new oo_tag($init[$i],true);
            $test->tags->stick($tag);
        }
        $test->dummyint = 1;
        $test->commit();
        
        oo_object::flush_cache();
        $read = oo_object::load_object_of($test->get_id());
        for ($i=0;$i<count($add);$i++) {
            $tag = new oo_tag($add[$i],true);
            $read->tags->stick($tag);
        }
        for ($i=0;$i<count($delete);$i++) {
            $tag = new oo_tag($delete[$i],true);
            $read->tags->remove($tag);
        }
        $read->commit();
        $this->assertEquals($changestr,$read->changestr);                
    }
    
    public function ChangeTagProvider() {
        return [
            [['TagA','TagB'],['TagChildA'],[],['TagA','TagB','TagChildA'],'ADD:TagChildA'],
            [['TagA','TagB'],[],['TagA'],['TagB'],'REMOVED:TagA'],
            [['TagA','TagB'],['TagChildA'],['TagA'],['TagB','TagChildA'],'ADD:TagChildAREMOVED:TagA'],
            [['TagA'],['TagC'],[],['TagA','TagB.TagC'],'ADD:TagB.TagC'],
            [['TagA','TagB.TagC'],[],['TagC'],['TagA'],'REMOVED:TagB.TagC'],
        ];
    }
}
