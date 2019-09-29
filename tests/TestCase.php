<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

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
/*        if (!self::$db_up ) {
            \Tests\setup_db();
            self::$db_up = true;
        } */
    }
    
    public function tearDown() {
        DB::connection()->setPdo(null);
        parent::tearDown();
    }

    protected function get_field($loader,$fieldname) {
        $match = '';
        if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            $index = $match['index'];
            return $loader->$name[$index]->$subfield;
        } else if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]\[(?P<index2>\w+)\]/',$fieldname,$match)) {
            $name = $match['name'];
            $index2 = $match['index2'];
            $index = $match['index'];
            return $loader->$name[$index][$index2];
        } else if (preg_match('/(?P<name>\w+)->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            return $loader->$name->$subfield;
        } if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]/',$fieldname,$match)){
            $name = $match['name'];
            $index = $match['index'];
            return $loader->$name[$index];
        }  else {
            return $loader->$fieldname;
        }
    }
    
    
}
