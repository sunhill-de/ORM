<?php

namespace Sunhill\ORM\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Database\Seeds\SimpleSeeder;

abstract class DBTestCase extends TestCase
{

    use RefreshDatabase;
    
    protected static $db_inited = false;
    
    public function setUp():void {
        parent::setUp();
        if (!static::$db_inited) {
            static::$db_inited = true;
            $this->do_migration();
            $this->do_seeding();
        }
        Objects::flush_cache();
    }
    
    /**
     * At least once per test it has to be rebuilt
     */
    public static function setUpBeforeClass() : void {
        parent::setUpBeforeClass();
        static::$db_inited = false;
    }
    
    protected function do_migration() {
        $this->artisan('migrate:fresh',['--path'=>'database/migrations/']);
        $this->artisan('migrate',['--path'=>'database/migrations/common']);
        $this->artisan('migrate',['--path'=>'database/migrations/simpletests']);
    }
    
    protected function do_seeding() {
        $this->seed(SimpleSeeder::class);        
    }
}
