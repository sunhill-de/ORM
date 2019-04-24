<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectDegradeTest extends ObjectCommon
{
    public function testOneStepDegration() {
        $test = new \Sunhill\Test\ts_thirdlevelchild;
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new \Sunhill\Test\ts_dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $test->childint = 1;
        $test->childchildint = 2;
        $tag = new \Sunhill\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->degrade('Sunhill\\Test\\ts_secondlevelchild');
        $new->commit();
        
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($id);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
        $this->assertEquals('Sunhill\\Test\ts_secondlevelchild',\Sunhill\Objects\oo_object::get_class_name_of($id));
    }
    
    public function testTwoStepDegration() {
        $test = new \Sunhill\Test\ts_thirdlevelchild;
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new \Sunhill\Test\ts_dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $test->childint = 1;
        $test->childchildint = 2;
        $tag = new \Sunhill\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->degrade('Sunhill\\Test\\ts_testparent');
        $new->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($id);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
        $this->assertEquals('Sunhill\\Test\ts_testparent',\Sunhill\Objects\oo_object::get_class_name_of($id));
    }
        
}
