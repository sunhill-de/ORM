<?php
/**
 * @file ScenarioWithSystem.php
 * An extension to scenarios that handle the system tables
 * Lang en
 * Reviewstatus: 2021-08-05
 * Localization: none required
 * Documentation: complete
 * Tests: tests/Unit/Scenarios/ScenariosWithSystemTest.php, tests/Feature/Scenarios/ScenarioWithSystemTest.php
 * Coverage: unknown
 * Dependencies: Functioning tag subsystem
 */

namespace Sunhill\ORM\Tests\Scenario;

use Sunhill\ORM\Facades\Tags;
use Sunhill\Basic\SunhillException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

trait ScenarioWithSystem {

  /**
   * This is called by the Test to setup set Tags
   */
  protected function SetupSystem() {
      $localdir = 'packages/orm/database/migrations/';
      Artisan::call('migrate:fresh',['--path'=>'database/migrations/']);
      Artisan::call('migrate',['--path'=>$localdir]);
  }
  
}
