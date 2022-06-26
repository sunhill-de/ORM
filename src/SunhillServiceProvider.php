<?php

namespace Sunhill\ORM;

use Illuminate\Support\ServiceProvider;
use \Sunhill\ORM\Managers\ClassManager;
use \Sunhill\ORM\Managers\ObjectManager;
use \Sunhill\ORM\Managers\TagManager;
use Sunhill\ORM\Console\MigrateObjects;
use Sunhill\ORM\Console\FlushCaches;
use Sunhill\Basic\Facades\Checks;

use Sunhill\ORM\Checks\OrmChecks;

use Sunhill\ORM\Managers\OperatorManager;

class SunhillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ClassManager::class, function () { return new ClassManager(); } );
        $this->app->alias(ClassManager::class,'classes');
        $this->app->singleton(ObjectManager::class, function () { return new ObjectManager(); } );
        $this->app->alias(ObjectManager::class,'objects');
        $this->app->singleton(TagManager::class, function () { return new TagManager(); } );
        $this->app->alias(TagManager::class,'tags');
        $this->app->singleton(OperatorManager::class, function () { return new OperatorManager(); } );
        $this->app->alias(OperatorManager::class,'operators');
    }
    
    public function boot()
    {
        Checks::InstallChecker(OrmChecks::class);
        
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','ORM');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateObjects::class,
            ]);
        } 

    }
}
