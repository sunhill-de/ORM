<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class ObjectDeleteTest extends ObjectCommon
{
 
    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('testchildren');
        $this->create_special_table('testparents');
        $this->create_special_table('dummies');
    }
    
    function testCantLoad() {
        $this->prepare_tables();
        $test = new \Sunhill\Test\ts_testchild;
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $test->childchar='ABC';
        $test->childint=123;
        $test->childfloat=1.23;
        $test->childtext='ABC DEF';
        $test->childdatetime='2001-01-01 01:01:01';
        $test->childdate='2011-01-01';
        $test->childtime='11:11:11';
        $test->childenum='testA';
        $add = new \Sunhill\Test\ts_dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $test->parentsarray[] = 'TEST';
        $test->childobject = $add;
        $test->childoarray[] = $add;
        $test->childsarray[] = 'TEST';
        $test->commit();
        $id = $test->get_id();
        $read = \Sunhill\Objects\oo_object::load_object_of($id);
        $read->delete(); 
        $this->assertFalse(\Sunhill\Objects\oo_object::load_object_of($id));
        return $id;
    }
    
    /**
     * @depends testCantLoad
     * @param Integer $id
     */
    function testObjectReferencesDeleted(Int $id) {
         $result = \App\objectobjectassign::where('container_id','=',$id)->first();
       $this->assertTrue(empty($result));
       $result = \App\objectobjectassign::where('element_id','=',$id)->first();
       $this->assertTrue(empty($result));
    }
    
    /** 
     * @depends testCantLoad
     */
    function testStringReferencesDeleted(int $id) {
        $result = \App\stringobjectassign::where('container_id','=',$id)->first();
        $this->assertTrue(empty($result));        
    }
}
