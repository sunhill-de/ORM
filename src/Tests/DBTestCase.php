<?php

namespace Sunhill\ORM\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Database\Seeds\SimpleSeeder;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Tests\Objects\ts_testchild;
use Sunhill\ORM\Tests\Objects\ts_testparent;
use Sunhill\ORM\Tests\Objects\ts_passthru;
use Sunhill\ORM\Tests\Objects\ts_secondlevelchild;
use Sunhill\ORM\Tests\Objects\ts_thirdlevelchild;
use Sunhill\ORM\Tests\Objects\ts_referenceonly;
use Sunhill\ORM\Tests\Objects\ts_objectunit;

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
        Classes::flushClasses();
        Classes::registerClass(ts_dummy::class);
        Classes::registerClass(ts_testparent::class);
        Classes::registerClass(ts_testchild::class);
        Classes::registerClass(ts_referenceonly::class);
        Classes::registerClass(ts_passthru::class);
        Classes::registerClass(ts_secondlevelchild::class);
        Classes::registerClass(ts_thirdlevelchild::class);
        Classes::registerClass(ts_objectunit::class);
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
