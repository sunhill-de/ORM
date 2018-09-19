<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

require_once(dirname(__FILE__).'/lib/ObjectTestScenario.php');

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    static $db_up = false;
    
    /**
     * Holds an application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->app = $this->createApplication();
        if (!self::$db_up ) {
            \Tests\setup_db();
            self::$db_up = true;
        }
    }
}
