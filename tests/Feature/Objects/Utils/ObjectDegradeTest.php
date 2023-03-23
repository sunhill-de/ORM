<?php

namespace Sunhill\ORM\Tests\Feature\Objects\Utils;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;

use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Tags;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Objects;

use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;


class ObjectDegradeTest extends DatabaseTestCase
{
    
    public function testOneStepDegration() {
        $test = new ThirdLevelChild;
        $add = new Dummy();
        $add->dummyint = 123;
        $test->testobject = $add;
        $test->testoarray[] = $add;
        $test->childint = 1;
        $test->childchildint = 2;
        $test->tags->stick('TagA');
        
        $test->commit();
        $id = $test->getID();
        
        $new = $test->degrade('secondlevelchild');
        $new->commit();
        
        Objects::flushCache();
        $read = Objects::load($id);
        $this->assertEquals(123,$read->testoarray[0]->dummyint);
        $this->assertEquals('secondlevelchild',Objects::getClassNameOf($id));
        $this->assertEquals(1,$read->childint);
    }
    
    public function testTwoStepDegration() {
        $test = new ThirdLevelChild;
        $add = new Dummy();
        $add->dummyint = 123;
        $test->testobject = $add;
        $test->testoarray[] = $add;
        $test->childint = 1;
        $test->childchildint = 2;
        $test->tags->stick('TagA');
        
        $test->commit();
        $id = $test->getID();
        $new = $test->degrade('referenceonly');
        $new->commit();
        Objects::flushCache();
        $read = Objects::load($id);
        $this->assertEquals(123,$read->testoarray[0]->dummyint);
        $this->assertEquals('referenceonly',Objects::getClassNameOf($id));
        return $test;
    }
       
    /**
     * @depends testTwoStepDegration
     * @param unknown $test
     */
    public function testTablesDeleted($test) {
        $result = DB::table('thirdlevelchildren')->where('id',$test->getID())->first();
        $this->assertTrue(empty($result));
        return $test;
    }
    /**
     * @depends testTwoStepDegration
     * @param unknown $test
     */
    public function testTableChildDeleted($test) {
        $result = DB::table('secondlevelchildren')->where('id',$test->getID())->first();
        $this->assertTrue(empty($result));
        return $test;
    }
    
    /**
     * @depends testTwoStepDegration
     * @param unknown $test
     */
    public function testObjectsDeleted($test) {
        $result = DB::table('objectobjectassigns')->where('container_id',$test->getID())->where('field','thirdlevelobject')->first();
        $this->assertTrue(empty($result));
        return $test;
    }
        
}
