<?php
/**
 * @file ScenarioWithSystem.php
 * An extension to scenarios that handle test object that need to be registered in the classmanager
 * Lang en
 * Reviewstatus: 2021-09-12
 * Localization: none required
 * Documentation: complete
 * Tests: tests/Unit/Scenarios/ScenariosWithRegistrationTest.php, tests/Feature/Scenarios/ScenarioWithRegistrationTest.php
 * Coverage: unknown
 * Dependencies: Functioning class manager
 */

namespace Sunhill\ORM\Tests\Scenario;

use Sunhill\ORM\Facades\Classes;
use Sunhill\Basic\SunhillException;

trait ScenarioWithRegistration {

  /**
   * This is called by the Test to setup the Registration 
   */
  protected function SetupRegistration() : void {
    $classes = $this->GetRegistration();
    foreach ($classes as $class) {
        $this->registerClass($class);
    }
  }
  
  protected function registerClass(string $class) {
      Classes::registerClass($class);      
  }
  
  abstract protected function GetRegistration() : array;
}
