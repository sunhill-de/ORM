<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Tests\Objects\ts_thirdlevelchild;

class ObjectDegradeTest extends DBTestCase
{
    
    public function testOneStepDegration() {
        $test = new ts_thirdlevelchild;
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new ts_dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $test->childint = 1;
        $test->childchildint = 2;
        $tag = new \Sunhill\ORM\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->degrade('secondlevelchild');
        $new->commit();
        
        Objects::flush_cache();
        $read = Objects::load($id);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
        $this->assertEquals('secondlevelchild',Objects::get_class_name_of($id));
    }
    
    public function testTwoStepDegration() {
        $test = new ts_thirdlevelchild;
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new ts_dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $test->childint = 1;
        $test->childchildint = 2;
        $test->thirdlevelobject = $add;
        $test->thirdlevelsarray[] = 'AAA';
        $test->thirdlevelsarray[] = 'BBB';
        $tag = new \Sunhill\ORM\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->degrade('testparent');
        $new->commit();
        Objects::flush_cache();
        $read = Objects::load($id);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
        $this->assertEquals('testparent',Objects::get_class_name_of($id));
        return $test;
    }
       
    /**
     * @depends testTwoStepDegration
     * @param unknown $test
     */
    public function testTablesDeleted($test) {
        $result = DB::table('thirdlevelchildren')->where('id',$test->get_id())->first();
        $this->assertTrue(empty($result));
        return $test;
    }
    /**
     * @depends testTwoStepDegration
     * @param unknown $test
     */
    public function testTableChildDeleted($test) {
        $result = DB::table('secondlevelchildren')->where('id',$test->get_id())->first();
        $this->assertTrue(empty($result));
        return $test;
    }
    
    /**
     * @depends testTwoStepDegration
     * @param unknown $test
     */
    public function testObjectsDeleted($test) {
        $result = DB::table('objectobjectassigns')->where('container_id',$test->get_id())->where('field','thirdlevelobject')->first();
        $this->assertTrue(empty($result));
        return $test;
    }
    
    
}