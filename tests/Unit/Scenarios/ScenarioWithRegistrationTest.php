<?php

/**
 * @file ScenarioWithRegistrationTest
 * phpunit: unit test
 * Tests: /src/Tests/Scenario/ScenarioWithRegistration.php
 * Reviewed: 2021-09-12
 * dependencies: Classes (but mocked), ts_dummy (just the class definition) 
 */
namespace Sunhill\Basic\Tests\Unit;

use Sunhill\Basic\Tests\SunhillTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithRegistration;
use Tests\CreatesApplication;
use Sunhill\Basic\SunhillException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\Objects\ts_dummy;

class ScenarioWithRegistrationUnitTestScenario extends ScenarioBase{

    use ScenarioWithRegistration;
    
    protected function GetRegistration() : array {
        return [ts_dummy::class];
    }
}

class ScenarioWithRegistrationTest extends SunhillTestCase
{
   
    use CreatesApplication;

    public function testRegisterClass() {
        Classes::shouldReceive('registerClass')->once()->with(ts_dummy::class);
        $test = new ScenarioWithRegistrationUnitTestScenario();
        $this->callProtectedMethod($test,'registerClass',[ts_dummy::class]);
    }
    
    public function testSetupRegistration() {
        Classes::shouldReceive('registerClass')->once()->with(ts_dummy::class);
        $test = new ScenarioWithRegistrationUnitTestScenario();
        $this->callProtectedMethod($test,'setupRegistration',[]);        
    }
}
