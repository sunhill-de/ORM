<?php

namespace Sunhill\ORM;

use Illuminate\Support\ServiceProvider;
use \Sunhill\ORM\Managers\class_manager;
use \Sunhill\ORM\Managers\object_manager;
use \Sunhill\ORM\Managers\tag_manager;
use Sunhill\ORM\Console\MigrateObjects;
use Sunhill\ORM\Console\FlushCaches;
use Sunhill\Basic\Facades\Checks;
use Sunhill\ORM\Checks\orm_checks;
use Sunhill\ORM\Managers\operator_manager;

class SunhillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(class_manager::class, function () { return new class_manager(); } );
        $this->app->alias(class_manager::class,'classes');
        $this->app->singleton(object_manager::class, function () { return new object_manager(); } );
        $this->app->alias(object_manager::class,'objects');
        $this->app->singleton(tag_manager::class, function () { return new tag_manager(); } );
        $this->app->alias(tag_manager::class,'tags');
        $this->app->singleton(operator_manager::class, function () { return new operator_manager(); } );
        $this->app->alias(operator_manager::class,'operators');
    }
    
    public function boot()
    {
        Checks::InstallChecker(orm_checks::class);
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','ORM');
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateObjects::class,
            ]);
            
          if (! class_exists('CreateAttributesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_attributes_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_attributes_table.php'),
                __DIR__ . '/../database/migrations/create_attributevalues_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_attributevalues_table.php'),
                __DIR__ . '/../database/migrations/create_caching_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_caching_table.php'),
                __DIR__ . '/../database/migrations/create_externalhooks_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_externalhooks_table.php'),
                __DIR__ . '/../database/migrations/create_objectobjectassigns_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_objectobjectassigns_table.php'),
                __DIR__ . '/../database/migrations/create_objects_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_objects_table.php'),
                __DIR__ . '/../database/migrations/create_stringobjectassigns_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_stringobjectassigns_table.php'),
                __DIR__ . '/../database/migrations/create_tagcache_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tagcache_table.php'),
                __DIR__ . '/../database/migrations/create_tagobjectassigns_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tagobjectassigns_table.php'),
                __DIR__ . '/../database/migrations/create_tags_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tags_table.php'),           
            ], 'migrations');
          }
          if (! class_exists('CreateSearchtestATable')) {
              $this->publishes([
                  __DIR__ . '/../database/migrations/searchtests/create_searchtestA_table.php.stub' => database_path('migrations/searchtests/' . date('Y_m_d_His', time()) . '_create_searchtestA_table.php'),
                  __DIR__ . '/../database/migrations/searchtests/create_searchtestB_table.php.stub' => database_path('migrations/searchtests/' . date('Y_m_d_His', time()) . '_create_searchtestB_table.php'),
                  __DIR__ . '/../database/migrations/searchtests/create_searchtestC_table.php.stub' => database_path('migrations/searchtests/' . date('Y_m_d_His', time()) . '_create_searchtestC_table.php'),
      
                  __DIR__ . '/../database/migrations/common/create_dummies_table.php.stub' => database_path('migrations/common/' . date('Y_m_d_His', time()) . '_create_dummies_table.php'),
                  
                  __DIR__ . '/../database/migrations/simpletests/create_objectunits_table.php.stub' => database_path('migrations/simpletests/' . date('Y_m_d_His', time()) . '_create_objectunits_table.php'),
                  __DIR__ . '/../database/migrations/simpletests/create_passthrus_table.php.stub' => database_path('migrations/simpletests/' . date('Y_m_d_His', time()) . '_create_passthrus_table.php'),
                  __DIR__ . '/../database/migrations/simpletests/create_referenceonlies_table.php.stub' => database_path('migrations/simpletests/' . date('Y_m_d_His', time()) . '_create_referenceonlies_table.php'),
                  __DIR__ . '/../database/migrations/simpletests/create_secondlevelchildren_table.php.stub' => database_path('migrations/simpletests/' . date('Y_m_d_His', time()) . '_create_secondlevelchildren_table.php'),
                  __DIR__ . '/../database/migrations/simpletests/create_testchildren_table.php.stub' => database_path('migrations/simpletests/' . date('Y_m_d_His', time()) . '_create_testchildren_table.php'),
                  __DIR__ . '/../database/migrations/simpletests/create_testparents_table.php.stub' => database_path('migrations/simpletests/' . date('Y_m_d_His', time()) . '_create_testparents_table.php'),
                  __DIR__ . '/../database/migrations/simpletests/create_thirdlevelchildren_table.php.stub' => database_path('migrations/simpletests/' . date('Y_m_d_His', time()) . '_create_thirdlevelchildren_table.php'),
              ], 'test-migrations');
          }
          if (! class_exists(Database\Seeders\AttriutesTableSeeder::class)) {          
              $this->publishes([       
                  __DIR__ . '/../database/seeds/AttributesTableSeeder.php' => database_path('seeders/AttributesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/DatabaseSeeder.php' => database_path('seeders/DatabaseSeeder.php'),
                  __DIR__ . '/../database/seeds/ExternalhooksTableSeeder.php' => database_path('seeders/ExternalhooksTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchCachingTableSeeder.php' => database_path('seeders/SearchCachingTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchDummiesTableSeeder.php' => database_path('seeders/SearchDummiesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchObjectObjectAssignsTableSeeder.php' => database_path('seeders/SearchObjectObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchSeeder.php' => database_path('seeders/SearchSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchStringObjectAssignsTableSeeder.php' => database_path('seeders/SearchStringObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchTagCacheTableSeeder.php' => database_path('seeders/SearchTagCacheTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchObjectObjectAssignsTableSeeder.php' => database_path('seeders/SearchObjectObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchObjectsTableSeeder.php' => database_path('seeders/SearchObjectsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchTagObjectAssignsTableSeeder.php' => database_path('seeders/SearchTagObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchTagsTableSeeder.php' => database_path('seeders/SearchTagsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchtestATableSeeder.php' => database_path('seeders/SearchtestATableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchtestBTableSeeder.php' => database_path('seeders/SearchtestBTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchtestCTableSeeder.php' => database_path('seeders/SearchtestCTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleAttributeValuesTableSeeder.php' => database_path('seeders/SimpleAttributeValuesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleCachingTableSeeder.php' => database_path('seeders/SimpleCachingTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleDummiesTableSeeder.php' => database_path('seeders/SimpleDummiesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleObjectObjectAssignsTableSeeder.php' => database_path('seeders/SimpleObjectObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleObjectsTableSeeder.php' => database_path('seeders/SimpleObjectsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimplePassthrusTableSeeder.php' => database_path('seeders/SimplePassthrusTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleSeeder.php' => database_path('seeders/SimpleSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleStringObjectAssignsTableSeeder.php' => database_path('seeders/SimpleStringObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleTagObjectAssignsTableSeeder.php' => database_path('seeders/SimpleTagObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleTestchildrenTableSeeder.php' => database_path('seeders/SimpleTestchildrenTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleTestparentsTableSeeder.php' => database_path('seeders/SimpleTestparentsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/TagCacheTableSeeder.php' => database_path('seeders/TagCacheTableSeeder.php'),
                  __DIR__ . '/../database/seeds/TagsTableSeeder.php' => database_path('seeders/TagsTableSeeder.php'),              
              ],'test-seeds');
          }
        }

    }
}
