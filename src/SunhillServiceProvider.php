<?php

namespace Sunhill\ORM;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use \Sunhill\ORM\Managers\ClassManager;
use \Sunhill\ORM\Managers\ObjectManager;
use Sunhill\ORM\Managers\StorageManager;
use \Sunhill\ORM\Managers\TagManager;
use \Sunhill\ORM\Managers\AttributeManager;
use Sunhill\ORM\Console\MigrateObjects;
use Sunhill\ORM\Console\FlushCaches;
use Sunhill\Basic\Facades\Checks;

use Sunhill\ORM\Checks\TagChecks;
use Sunhill\ORM\Checks\ObjectChecks;
use Sunhill\ORM\Facades\Tags;

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
        $this->app->singleton(AttributeManager::class, function () { return new AttributeManager(); } );
        $this->app->alias(AttributeManager::class,'attributes');
        $this->app->singleton(OperatorManager::class, function () { return new OperatorManager(); } );
        $this->app->alias(OperatorManager::class,'operators');
        $this->app->singleton(StorageManager::class, function () { return new StorageManager(); } );
        $this->app->alias(StorageManager::class,'storage');
    }
    
    public function boot()
    {
        Checks::InstallChecker(TagChecks::class);
        Checks::InstallChecker(ObjectChecks::class);
        
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','ORM');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateObjects::class,
            ]);
        } 

        Collection::macro('getTags', function() {
            return $this->map(function(\StdClass $value) {
               return Tags::loadTag($value->id); 
            });
        });
        Collection::macro('getObjects', function() {
                
        });
    }
}
