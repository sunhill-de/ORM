<?php

namespace Sunhill\Basic\Tests\Unit;

use Sunhill\Basic\Tests\SunhillOrchestraTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithObjects;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;
use  Sunhill\ORM\Tests\Objects\Dummy;
use  Sunhill\ORM\Tests\Objects\TestParent;
use Sunhill\Basic\SunhillException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Tags;

class ScenarioWithObjectsUnitTestScenario extends ScenarioBase{

    use ScenarioWithObjects;
             
    public function GetObjects() {
        return [
        ];
    }
}

class ScenarioWithObjectsTest extends SunhillOrchestraTestCase
{
   
    public function setUp() : void {
        parent::setUp();
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        Tags::clearTags();
        Tags::addTag('TagA');
        Tags::addTag('TagB');
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key,dummyint int)');
    }
    
    public function testSetReference() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $dummy = new Dummy();
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
        
        $dummy = new Dummy();
        $dummy->dummyint = 2;

        $this->callProtectedMethod($test,'storeReference',['test',$dummy]);
        return $test;
    }
    
    public function testHandleField() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy();
        
        $this->callProtectedMethod($test,'handleField',[$dummy,'dummyint',12]);
        
        $this->assertEquals(12,$dummy->dummyint);
    }
    
    public function testHandleFieldWithNull() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy();
        $dummy->dummyint = 1;
        
        $this->callProtectedMethod($test,'handleField',[$dummy,'dummyint',null]);
        
        $this->assertEquals(1,$dummy->dummyint);
    }
    
    public function testHandleTagWithString() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy();
        
        $this->callProtectedMethod($test,'handleTags',[$dummy,'TagA']);
        
        $this->assertEquals('TagA',$dummy->tags[0]);
    }
    
    public function testHandleTagWithArray() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy();
        
        $this->callProtectedMethod($test,'handleTags',[$dummy,['TagA','TagB']]);
        
        $this->assertEquals('TagB',$dummy->tags[1]);
    }
    
    public function testHandleTagWithNull() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy();
        
        $this->callProtectedMethod($test,'handleTags',[$dummy,null]);
        
        $this->assertEquals(0,count($dummy->tags));
    }
    
    public function testHandleReference() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy();
        $dummy->dummyint = 12;
        $this->callProtectedMethod($test,'storeReference',['dummy',$dummy]);
        
        $parent = new TestParent();
        
        $this->callProtectedMethod($test,'handleReference',[$parent,'parentobject',"=>dummy"]);
        
        $this->assertEquals(12,$parent->parentobject->dummyint);
    }

    public function testHandleArrayOfStrings() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new TestParent();
        
        $this->callProtectedMethod($test,'handleArray',[$parent,'parentsarray',['A','B','C']]);
        
        $this->assertEquals('B',$parent->parentsarray[1]);
    }

    public function testHandleArrayOfObjects() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new TestParent();
        $dummy1 = new Dummy(); $dummy1->dummyint = 12;
        $dummy2 = new Dummy(); $dummy2->dummyint = 23;
        $dummy3 = new Dummy(); $dummy3->dummyint = 34;
        
        $this->callProtectedMethod($test,'storeReference',['dummy1',$dummy1]);
        $this->callProtectedMethod($test,'storeReference',['dummy2',$dummy2]);
        $this->callProtectedMethod($test,'storeReference',['dummy3',$dummy3]);

        $this->callProtectedMethod($test,'handleArray',[$parent,'parentoarray',["=>dummy1","=>dummy2","=>dummy3"]]);
        
        $this->assertEquals(23,$parent->parentoarray[1]->dummyint);
    }

    public function testHandleFields() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $parent = new TestParent();
        $dummy1 = new Dummy(); $dummy1->dummyint = 12;
        $dummy2 = new Dummy(); $dummy2->dummyint = 23;
        $dummy3 = new Dummy(); $dummy3->dummyint = 34;
        
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
        
        $parent = new TestParent();
        $dummy1 = new Dummy(); $dummy1->dummyint = 12;
        $dummy2 = new Dummy(); $dummy2->dummyint = 23;
        $dummy3 = new Dummy(); $dummy3->dummyint = 34;
        
        $this->callProtectedMethod($test,'storeReference',['dummy1',$dummy1]);
        $this->callProtectedMethod($test,'storeReference',['dummy2',$dummy2]);
        $this->callProtectedMethod($test,'storeReference',['dummy3',$dummy3]);

        $this->callProtectedMethod($test,'handleFields',[$parent,['parentint','parentobject','parentsarray'],[null,null,null]]);
        
        $this->assertEquals(null,$parent->parentobject);
    }
 
    public function testHandleObject() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy(); $dummy->dummyint = 12;
        
        $this->callProtectedMethod($test,'handleObject',['dummy','test',['dummyint'],[12]]);

        $this->assertEquals(12,$this->callProtectedMethod($test,'getReference',['test'])->dummyint);
    }

    public function testHandleObjectWithoutReference() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new Dummy(); $dummy->dummyint = 12;
        
        $this->callProtectedMethod($test,'handleObject',['dummy',1,['dummyint'],[12]]);
        $this->assertEquals(0,count($this->getProtectedProperty($test,'references')));
    }
                                                        
    public function testHandleObjectWithError() {
        $this->expectException(SunhillException::class);

        $test = new ScenarioWithObjectsUnitTestScenario();
        $this->callProtectedMethod($test,'handleObject',['testparent',1,['parentint','parentobject','parentsarray'],[null,null]]);        
    }

    public function testHandleClass() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $this->callProtectedMethod($test,'handleClass',['dummy',[['dummyint'],['test'=>[1],[2],'test3'=>[3]]]]);
        
        $this->assertEquals(3,$this->callProtectedMethod($test,'getReference',['test3'])->dummyint);
        
    }
                                                        
    public function testHandleClassWithError() {
        $this->expectException(SunhillException::class);

        $test = new ScenarioWithObjectsUnitTestScenario();
        
        $this->callProtectedMethod($test,'handleClass',['testparent',[['parentint','parentobject']]]);
    }

}
