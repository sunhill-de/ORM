<?php

namespace Sunhill\Basic\Tests\Unit;

use Sunhill\Basic\Tests\SunhillTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithObjects;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;
use  Sunhill\ORM\Tests\Objects\ts_dummy;
use  Sunhill\ORM\Tests\Objects\ts_testparent;

class ScenarioWithObjectsUnitTestScenario extends ScenarioBase{

    use ScenarioWithObjects;
             
    public function GetObjects() {
        return [
        ];
    }
}

class ScenarioWithObjectsTest extends SunhillTestCase
{
   
    use CreatesApplication;
    
    public function testSetReference() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $dummy = new ts_dummy();
        $dummy->dummyint = 1;
        
        $this->callProtectedMethod($test,'storeReference',['test',$dummy]);
        
        $this->assertEquals(1,$this->getProtectedProperty($test,'references')['test']->dummyint);
        return $test;
    }
    
    /**
     * @depends testSetReference
     */
    public function testGetReference($test) {
        $this->assertEquals(1,$this->callProtectedMethod($test,'getReference',['test'])->dummyint);
        return $test;
    }
    
    /**
     * @depends testSetReference
     */
    public function testGetReferenceWithReferenceString($test) {
        $this->assertEquals(1,$this->callProtectedMethod($test,'getReference',['=>test'])->dummyint);
        return $test;
    }
    
    /**
     * @depends testSetReference
     */
    public function testGetUnknownReference($test) {
        $this->expectException(SunhillException::class);
        $this->callProtectedMethod($test,'getReference',['notexisting'])->dummyint;
        return $test;
    }
       
    /**
     * @depends testSetReference
     */
    public function testAlreadyUsedReference($test) {
        $this->expectException(SunhillException::class);
        
        $dummy = new dummy();
        $dummy->dummyint = 2;

        $this->callProtectedMethod($test,'storeReference',['test',$dummy]);
        return $test;
    }
    
    public function testHandleField() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new ts_dummy();
        
        $this->callProtectedMethod($test,'handleField',[$dummy,'dummyint',12]);
        
        $this->assertEquals(12,$dummy->dummyint);
    }
    
    public function testHandleFieldWithNull() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new ts_dummy();
        $dummy->dummyint = 1;
        
        $this->callProtectedMethod($test,'handleField',[$dummy,'dummyint',null]);
        
        $this->assertEquals(1,$dummy->dummyint);
    }
    
    public function testHandleTagWithString() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new ts_dummy();
        
        $this->callProtectedMethod($test,'handleTag',[$dummy,'TagA']);
        
        $this->assertEquals('TagA',$dummy->tags[0]);
    }
    
    public function testHandleTagWithArray() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new ts_dummy();
        
        $this->callProtectedMethod($test,'handleTag',[$dummy,['TagA','TagB']]);
        
        $this->assertEquals('TagB',$dummy->tags[1]);
    }
    
    public function testHandleTagWithNull() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new ts_dummy();
        
        $this->callProtectedMethod($test,'handleTag',[$dummy,null]);
        
        $this->assertEquals(0,count($dummy->tags));
    }
    
    public function testHandleReference() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new ts_dummy();
        $dummy->dummyint = 12;
        
        $parent = new ts_testparent();
        
        $this->callProtectedMethod($test,'handleReference',[$dummy,'parentobject',$dummy);
        
        $this->assertEquals(12,$parent->parentobject->dummyint);
    }

    public function testHandleReferenceWithNull() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new ts_testparent();
        
        $this->callProtectedMethod($test,'handleReference',[$dummy,'parentobject',null);
        
        $this->assertEquals(null,$parent->parentobject);
    }

    public function testHandleArrayOfStrings() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new ts_testparent();
        
        $this->callProtectedMethod($test,'handleArray',[$parent,'parentsarray',['A','B','C']);
        
        $this->assertEquals('B',$parent->parentsarray[1]);
    }

    public function testHandleArrayOfObjects() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new ts_testparent();
        $dummy1 = new ts_dummy(); $dummy1->dummyint = 12;
        $dummy2 = new ts_dummy(); $dummy2->dummyint = 23;
        $dummy3 = new ts_dummy(); $dummy3->dummyint = 34;
        
        $this->callProtectedMethod($test,'storeReference',['dummy1',$dummy1]);
        $this->callProtectedMethod($test,'storeReference',['dummy2',$dummy2]);
        $this->callProtectedMethod($test,'storeReference',['dummy3',$dummy3]);

        $this->callProtectedMethod($test,'handleArray',[$parent,'parentoarray',["=>dummy1","=>dummy2","=>dummy3"]);
        
        $this->assertEquals(23,$parent->parentoarray[1]->dummyint);
    }

    public function testHandleFields() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new ts_testparent();
        $dummy1 = new ts_dummy(); $dummy1->dummyint = 12;
        $dummy2 = new ts_dummy(); $dummy2->dummyint = 23;
        $dummy3 = new ts_dummy(); $dummy3->dummyint = 34;
        
        $this->callProtectedMethod($test,'storeReference',['dummy1',$dummy1]);
        $this->callProtectedMethod($test,'storeReference',['dummy2',$dummy2]);
        $this->callProtectedMethod($test,'storeReference',['dummy3',$dummy3]);

        $this->callProtectedMethod($test,'handleFields',[$parent,['parentint','parentobject','parentsarray'],[12,"=>dummy2",['A','B','C']]]);
        
        $this->assertEquals(12,$parent->parentint);
        $this->assertEquals(23,$parent->parentobject->dummyint);
        $this->assertEquals('B',$parent->parentsarray[1]);                                                         
    }
                                                        
    public function testHandleFieldsWithNull() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new ts_testparent();
        $dummy1 = new ts_dummy(); $dummy1->dummyint = 12;
        $dummy2 = new ts_dummy(); $dummy2->dummyint = 23;
        $dummy3 = new ts_dummy(); $dummy3->dummyint = 34;
        
        $this->callProtectedMethod($test,'storeReference',['dummy1',$dummy1]);
        $this->callProtectedMethod($test,'storeReference',['dummy2',$dummy2]);
        $this->callProtectedMethod($test,'storeReference',['dummy3',$dummy3]);

        $this->callProtectedMethod($test,'handleFields',[$parent,['parentint','parentobject','parentsarray'],[null,null,null]]]);
        
        $this->assertEquals(null,$parent->parentobject);
    }
 
    public function testHandleObject() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy1 = new ts_dummy(); $dummy1->dummyint = 12;
        $dummy2 = new ts_dummy(); $dummy2->dummyint = 23;
        $dummy3 = new ts_dummy(); $dummy3->dummyint = 34;
        
        $this->callProtectedMethod($test,'storeReference',['dummy1',$dummy1]);
        $this->callProtectedMethod($test,'storeReference',['dummy2',$dummy2]);
        $this->callProtectedMethod($test,'storeReference',['dummy3',$dummy3]);

        $this->callProtectedMethod($test,'handleObject',['ts_testparebt,'test',['parentint','parentobject','parentsarray'],[12,"=>dummy2",['A','B','C']]]);

        $this->assertEquals(23,$this->callProtectedMethod($test,'getReference',['test'])->parentobject->dummyint);
    }

    public function testHandleObjectWithoutReference() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $this->callProtectedMethod($test,'handleObject',['ts_testparent',1,['parentint','parentobject','parentsarray'],[null,null,null]]]);
        $this->assertEquals(0,count($this->getProtectedProperty('references')));
    }
                                                        
    public function testHandleObjectWithError() {
        $this->expectException(SunhillException::class);

        $test = new ScenarioWithObjectsUnitTestScenario();
        $this->callProtectedMethod($test,'handleObject',['ts_testparent',1,['parentint','parentobject','parentsarray'],[null,null]]]);        
    }

    public function testHandleClass() {
        $test = new ScenarioWithObjectsUnitTestScenario();
    }
                                                        
    public function testHandleClassWithError() {
        $this->expectException(SunhillException::class);

        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $this->callProtectedMethod($test,'handleClass',['ts_testparent',[['parentint','parentobject']]]);
    }

}
