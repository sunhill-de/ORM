<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

require_once(dirname(__FILE__).'/../lib/ObjectTestScenario.php');

class ObjectCommon extends TestCase
{

		protected function setup_scenario() {
			setup_db();
		}
		
		protected function teardown_scenario() {
			teardown_db();
		}
		
}
