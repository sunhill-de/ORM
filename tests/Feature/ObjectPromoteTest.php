<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\DBTestCase;
use Sunhill\Objects\oo_object;

class ObjectPromoteTest extends DBTestCase
{
    
    /**
     * @dataProvider InheritanceProvider
     */
    public function testInheritance($classname,$expected) {
        $test = new $classname;
        $this->assertEquals($expected,$test->get_inheritance());
    }
    
    public function InheritanceProvider(){    
        return [
            ['Sunhill\Test\\ts_testparent',['Sunhill\Objects\oo_object']],
            ['Sunhill\Test\\ts_testchild',['Sunhill\Test\\ts_testparent','Sunhill\Objects\oo_object']],
            ['Sunhill\Test\\ts_passthru',['Sunhill\Test\\ts_testparent','Sunhill\Objects\oo_object']],
            ['Sunhill\Test\\ts_secondlevelchild',['Sunhill\Test\\ts_passthru','Sunhill\Test\\ts_testparent','Sunhill\Objects\oo_object']],
            ['Sunhill\Test\\ts_thirdlevelchild',['Sunhill\Test\\ts_secondlevelchild','Sunhill\Test\\ts_passthru','Sunhill\Test\\ts_testparent','Sunhill\Objects\oo_object']]
        ];
    }
    
    public function testOneStepPromotion() {
        $test = new \Sunhill\Test\ts_secondlevelchild;
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
        $tag = new \Sunhill\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\Test\\ts_thirdlevelchild');
        $new->commit(); 
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($id);
        $this->assertEquals(1,$read->childint);
        $this->assertEquals(2,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
        $this->assertEquals('123A',$read->parentcalc);
        $this->assertEquals(123,$read->parentobject->dummyint);
    }
    
    public function testTwoStepPromotion() {
         $test = new \Sunhill\Test\ts_passthru();
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
        $tag = new \Sunhill\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\Test\\ts_thirdlevelchild');
        $new->commit();
        \Sunhill\Objects\oo_object::flush_cache();
        $read = \Sunhill\Objects\oo_object::load_object_of($id);
        $this->assertEquals(2,$read->childint);
        $this->assertEquals(4,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
    }
    
   public function testWrongInhertiance() {
        $this->expectException(\Sunhill\Objects\ObjectException::class);
        $test = new \Sunhill\Test\ts_passthru();
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\Test\\ts_testchild');        
    }
    
    public function testNotExistingClassInhertiance() {
        $this->expectException(\Sunhill\Objects\ObjectException::class);
        $test = new \Sunhill\Test\ts_passthru();
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\Test\\notexisting');
    }
    
}
