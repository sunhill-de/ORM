<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Utils;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Tests\Objects\SecondLevelChild;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\Passthru;

class ObjectPromoteTest extends DBTestCase
{
    
    /**
     * @dataProvider InheritanceProvider
     */
    public function testInheritance($classname,$expected) {
        $test = new $classname;
        $this->assertEquals($expected,$test->getInheritance());
    }
    
    public function InheritanceProvider(){    
        return [
            ['Sunhill\ORM\Tests\\Objects\\TestParent',['object']],
            ['Sunhill\ORM\Tests\\Objects\\TestChild',['testparent','object']],
            ['Sunhill\ORM\Tests\\Objects\\Passthru',['testparent','object']],
            ['Sunhill\ORM\Tests\\Objects\\SecondLevelChild',['passthru','testparent','object']],
            ['Sunhill\ORM\Tests\\Objects\\ThirdLevelChild',['secondlevelchild','passthru','testparent','object']]
        ];
    }
    
    public function testOneStepPromotion() {
        $test = new SecondLevelChild;
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new Dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        $test->childint = 1;
        Tags::addTag('TestTag');
        $test->tags->stick('TestTag');
        $test->commit();
        $id = $test->getID();
        $new = $test->promote('Sunhill\\ORM\\Tests\\Objects\\ThirdLevelChild');
        $new->commit(); 
        Objects::flushCache();
        $read = Objects::load($id);
        $this->assertEquals(1,$read->childint);
        $this->assertEquals(2,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
        $this->assertEquals('123A',$read->parentcalc);
        $this->assertEquals(123,$read->parentobject->dummyint);
    }
    
    public function testTwoStepPromotion() {
         $test = new Passthru();
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $add = new Dummy();
        $add->dummyint = 123;
        $test->parentobject = $add;
        $test->parentoarray[] = $add;
        Tags::addTag('TestTag');
        $test->tags->stick('TestTag');
        $test->commit();
        $id = $test->getID();
        $new = $test->promote('\\Sunhill\\ORM\\Tests\\Objects\\ThirdLevelChild');
        $new->commit();
       Objects::flushCache();
        $read = Objects::load($id);
        $this->assertEquals(2,$read->childint);
        $this->assertEquals(4,$read->childchildint);
        $this->assertEquals(123,$read->parentoarray[0]->dummyint);
    }
    
   public function testWrongInhertiance() {
        $this->expectException(ORMException::class);
        $test = new Passthru();
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $test->commit();
        $id = $test->getID();
        $new = $test->promote('TestChild');        
    }
    
    public function testNotExistingClassInhertiance() {
        $this->expectException(ORMException::class);
        $test = new Passthru();
        $test->parentchar='ABC';
        $test->parentint=123;
        $test->parentfloat=1.23;
        $test->parenttext='ABC DEF';
        $test->parentdatetime='2001-01-01 01:01:01';
        $test->parentdate='2011-01-01';
        $test->parenttime='11:11:11';
        $test->parentenum='testA';
        $test->commit();
        $id = $test->getID();
        $new = $test->promote('notexisting');
    }
    
}
