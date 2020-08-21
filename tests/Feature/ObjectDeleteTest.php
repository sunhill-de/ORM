<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\Objects\oo_object;
use Tests\TestCase;

class ObjectDeleteTest extends TestCase
{
    public function setUp():void {
        parent::setUp();
        $this->seed('SimpleSeeder');
        oo_object::flush_cache();
    }
    
    function testCantLoad() {
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
       $result = DB::table('objectobjectassigns')->where('container_id','=',$id)->first();
       $this->assertTrue(empty($result));
       $result = DB::table('objectobjectassigns')->where('element_id','=',$id)->first();
       $this->assertTrue(empty($result));
    }
    
    /** 
     * @depends testCantLoad
     */
    function testStringReferencesDeleted(int $id) {
        $result = DB::table('stringobjectassigns')->where('container_id','=',$id)->first();
        $this->assertTrue(empty($result));        
    }
}
