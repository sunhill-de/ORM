<?php

namespace Sunhill\Basic\Tests\Unit;

use Sunhill\Basic\Tests\SunhillTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithObjects;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;

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
        
        $dummy = new dummy();
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
        $dummy = new dummy();
        
        $this->callProtectedMethod($test,'handleField',[$dummy,'dummyint',12]);
        
        $this->assertEquals(12,$dummy->dummyint);
    }
    
    public function testHandleFieldWithNull() {
        $test = new ScenarioWithObjectsUnitTestScenario();
        $dummy = new dummy();
        $dummy->dummyint = 1;
        
        $this->callProtectedMethod($test,'handleField',[$dummy,'dummyint',null]);
        
        $this->assertEquals(1,$dummy->dummyint);
    }
    
}
