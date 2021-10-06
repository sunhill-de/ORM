<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\Basic\Tests\SunhillScenarioTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithRegistration;
use Tests\CreatesApplication;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\Basic\SunhillException;

class ScenarioWithRegistrationFeatureTestScenario extends ScenarioBase{

    use ScenarioWithRegistration;
             
    protected $Requirements = [
        'Registration'=>[
            'destructive'=>true,
        ],
    ];
    
    public function GetRegistration() {
        return [
            ts_dummy::class
        ];
    }
    
}

class ScenarioWithRegistrationTest extends SunhillScenarioTestCase
{
   
    static protected $ScenarioClass = 'Sunhill\\ORM\\Tests\\Feature\\ScenarioWithRegistrationFeatureTestScenario';
    
    use CreatesApplication;

    protected function GetScenarioClass() {
        return ScenarioWithRegistrationFeatureTestScenario::class;    
    }
    
    public function testRegistedBefore() {
        Classes::flushClasses();
        $this->expectException(SunhillException::class);
        Classes::getNamespaceOfClass('dummy');
    }
    
    public function testRegisteredAfter() {
        $this->assertEquals(ts_dummy::class,Classes::getNamespaceOfClass('dummy'));
    }
}