<?php

namespace Sunhill\ORM\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Database\Seeders\DatabaseSeeder;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;

class DatabaseTestCase extends TestCase
{

    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->registerClasses();
        $this->seed(DatabaseSeeder::class);
    }
    
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../../tests/Database/migrations');
    }
    
    protected function registerClasses()
    {
        Objects::flushCache();
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);        
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        Classes::registerClass(TestSimpleChild::class);
    }
        
}