<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Utils;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\ORMException;

use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;

class ObjectPromoteTest extends DatabaseTestCase
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
            [TestParent::class,['object']],
            [TestChild::class,['testparent','object']],
            [ReferenceOnly::class,['object']],
            [SecondLevelChild::class,['referenceonly','object']],
            [ThirdLevelChild::class,['secondlevelchild','referenceonly','object']]
        ];
    }
    
    public function testOneStepPromotion() {
        $test = new SecondLevelChild;
        $test->childint=123;
        $add = new Dummy();
        $add->dummyint = 123;
        $test->testobject = $add;
        $test->testoarray[] = $add;
        $test->tags->stick('TagA');
        
        $test->commit();        
        $id = $test->getID();
        
        $new = $test->promote(ThirdLevelChild::class);
        $new->commit(); 
        
        Objects::flushCache();        
        $read = Objects::load($id);
        
        $this->assertEquals(123,$read->childint);
        $this->assertEquals(246,$read->childchildint);
        $this->assertEquals(123,$read->testoarray[0]->dummyint);
        $this->assertEquals(123,$read->testobject->dummyint);
    }
    
    public function testTwoStepPromotion() {
        $test = new ReferenceOnly();
        $add = new Dummy();
        $add->dummyint = 123;
        $test->testobject = $add;
        $test->testoarray[] = $add;
        $test->tags->stick('TagA');
        
        $test->commit();
        $id = $test->getID();
        
        $new = $test->promote(ThirdLevelchild::class);
        $new->commit();
        
        Objects::flushCache();        
        $read = Objects::load($id);
        
        $this->assertEquals(2,$read->childint);
        $this->assertEquals(4,$read->childchildint);
        $this->assertEquals(123,$read->testoarray[0]->dummyint);
    }
    
   public function testWrongInhertiance() {
        $this->expectException(ORMException::class);
        $test = new ReferenceOnly();
        $test->commit();
        $id = $test->getID();
        $new = $test->promote('TestChild');        
    }
    
    public function testNotExistingClassInhertiance() {
        $this->expectException(ORMException::class);
        $test = new ReferenceOnly();
        $test->commit();
        $id = $test->getID();
        $new = $test->promote('notexisting');
    }
    
}
