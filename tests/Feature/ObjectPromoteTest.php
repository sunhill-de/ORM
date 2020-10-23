<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Facades\Objects;

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
            ['Sunhill\ORM\Test\\ts_testparent',['object']],
            ['Sunhill\ORM\Test\\ts_testchild',['testparent','object']],
            ['Sunhill\ORM\Test\\ts_passthru',['testparent','object']],
            ['Sunhill\ORM\Test\\ts_secondlevelchild',['passthru','testparent','object']],
            ['Sunhill\ORM\Test\\ts_thirdlevelchild',['secondlevelchild','passthru','testparent','object']]
        ];
    }
    
    public function testOneStepPromotion() {
        $test = new \Sunhill\ORM\Test\ts_secondlevelchild;
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new \Sunhill\ORM\Test\ts_dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $test->childint = 1;
        $tag = new \Sunhill\ORM\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\ORM\\Test\\ts_thirdlevelchild');
        $new->commit(); 
        Objects::flush_cache();
        $read = Objects::load($id);
        $this->assertEquals(1,$read->childint);
        $this->assertEquals(2,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
        $this->assertEquals('123A',$read->parentcalc);
        $this->assertEquals(123,$read->parentobject->dummyint);
    }
    
    public function testTwoStepPromotion() {
         $test = new \Sunhill\ORM\Test\ts_passthru();
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new \Sunhill\ORM\Test\ts_dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $tag = new \Sunhill\ORM\Objects\oo_tag('TestTag',true);
        $test->tags->stick($tag);
        $test->commit();
        $id = $test->get_id();
        $new = $test->promote('\\Sunhill\\ORM\\Test\\ts_thirdlevelchild');
        $new->commit();
       Objects::flush_cache();
        $read = Objects::load($id);
        $this->assertEquals(2,$read->childint);
        $this->assertEquals(4,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
    }
    
   public function testWrongInhertiance() {
        $this->expectException(\Sunhill\ORM\Managers\ObjectManagerException::class);
        $test = new \Sunhill\ORM\Test\ts_passthru();
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
        $this->expectException(\Sunhill\ORM\Managers\ObjectManagerException::class);
        $test = new \Sunhill\ORM\Test\ts_passthru();
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
        $new = $test->promote('\\Sunhill\\ORM\\Test\\notexisting');
    }
    
}
