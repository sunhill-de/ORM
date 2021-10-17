<?php

namespace Sunhill\ORM\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Database\Seeds\SimpleSeeder;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\TestChild;
use Sunhill\ORM\Tests\Objects\TestParent;
use Sunhill\ORM\Tests\Objects\Passthru;
use Sunhill\ORM\Tests\Objects\SecondLevelChild;
use Sunhill\ORM\Tests\Objects\ThirdLevelChild;
use Sunhill\ORM\Tests\Objects\ReferenceOnly;
use Sunhill\ORM\Tests\Objects\ObjectUnit;

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
        Objects::flushCache();
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        Classes::registerClass(ReferenceOnly::class);
        Classes::registerClass(Passthru::class);
        Classes::registerClass(SecondLevelChild::class);
        Classes::registerClass(ThirdLevelChild::class);
        Classes::registerClass(ObjectUnit::class);
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
