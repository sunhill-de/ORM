<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectPromoteTest extends ObjectCommon
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
            ['Sunhill\Test\\ts_testparent',[]],
            ['Sunhill\Test\\ts_testchild',['Sunhill\Test\\ts_testparent']],
            ['Sunhill\Test\\ts_passthru',['Sunhill\Test\\ts_testparent']],
            ['Sunhill\Test\\ts_secondlevelchild',['Sunhill\Test\\ts_passthru','Sunhill\Test\\ts_testparent']],
            ['Sunhill\Test\\ts_thirdlevelchild',['Sunhill\Test\\ts_secondlevelchild','Sunhill\Test\\ts_passthru','Sunhill\Test\\ts_testparent']]
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
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\Test\\ts_thirdlevelchild');
        $new->commit(); 
        $read = new \Sunhill\Test\ts_thirdlevelchild;
        $read = $read->load($id);
        $this->assertEquals(1,$read->childint);
        $this->assertEquals(2,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
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
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\Test\\ts_thirdlevelchild');
        $new->commit();
        $read = new \Sunhill\Test\ts_thirdlevelchild;
        $read = $read->load($id);
        $this->assertEquals(2,$read->childint);
        $this->assertEquals(4,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
    }
    
    /**
     * @expectedException Sunhill\Objects\ObjectException
     */
    public function testWrongInhertiance() {
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
    
    /**
     * @expectedException \Sunhill\Objects\ObjectException
     */
    public function testNotExistingClassInhertiance() {
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
