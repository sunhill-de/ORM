<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Objects\oo_object;

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
        oo_object::flush_cache();
    }
    
    protected function do_migration() {
        $this->artisan('migrate:fresh',['--path'=>'database/migrations/']);
        $this->artisan('migrate',['--path'=>'database/migrations/common']);
        $this->artisan('migrate',['--path'=>'database/migrations/simpletests']);
    }
    
    protected function do_seeding() {
        $this->seed('SimpleSeeder');        
    }
}
