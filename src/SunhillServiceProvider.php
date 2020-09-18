<?php

namespace Sunhill\ORM;

use Illuminate\Support\ServiceProvider;
use \Sunhill\ORM\Managers\class_manager;

class SunhillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(class_manager::class, function () { return new class_manager(); } );
        $this->app->alias(class_manager::class,'classes');
    }
    
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','ORM',);
        if ($this->app->runningInConsole()) {

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
          if (! class_exists('AttriutesTableSeeder')) {          
              $this->publishes([       
                  __DIR__ . '/../database/seeds/AttributesTableSeeder.php' => database_path('seeds/AttributesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/DatabaseSeeder.php' => database_path('seeds/DatabaseSeeder.php'),
                  __DIR__ . '/../database/seeds/ExternalhooksTableSeeder.php' => database_path('seeds/ExternalhooksTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchCachingTableSeeder.php' => database_path('seeds/SearchCachingTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchDummiesTableSeeder.php' => database_path('seeds/SearchDummiesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchObjectObjectAssignsTableSeeder.php' => database_path('seeds/SearchObjectObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchSeeder.php' => database_path('seeds/SearchSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchStringObjectAssignsTableSeeder.php' => database_path('seeds/SearchStringObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchTagCacheTableSeeder.php' => database_path('seeds/SearchTagCacheTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchObjectObjectAssignsTableSeeder.php' => database_path('seeds/SearchObjectObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchObjectsTableSeeder.php' => database_path('seeds/SearchObjectsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchTagObjectAssignsTableSeeder.php' => database_path('seeds/SearchTagObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchTagsTableSeeder.php' => database_path('seeds/SearchTagsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchtestATableSeeder.php' => database_path('seeds/SearchtestATableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchtestBTableSeeder.php' => database_path('seeds/SearchtestBTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SearchtestCTableSeeder.php' => database_path('seeds/SearchtestCTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleAttributeValuesTableSeeder.php' => database_path('seeds/SimpleAttributeValuesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleCachingTableSeeder.php' => database_path('seeds/SimpleCachingTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleDummiesTableSeeder.php' => database_path('seeds/SimpleDummiesTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleObjectObjectAssignsTableSeeder.php' => database_path('seeds/SimpleObjectObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleObjectsTableSeeder.php' => database_path('seeds/SimpleObjectsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimplePassthrusTableSeeder.php' => database_path('seeds/SimplePassthrusTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleSeeder.php' => database_path('seeds/SimpleSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleStringObjectAssignsTableSeeder.php' => database_path('seeds/SimpleStringObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleTagObjectAssignsTableSeeder.php' => database_path('seeds/SimpleTagObjectAssignsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleTestchildrenTableSeeder.php' => database_path('seeds/SimpleTestchildrenTableSeeder.php'),
                  __DIR__ . '/../database/seeds/SimpleTestparentsTableSeeder.php' => database_path('seeds/SimpleTestparentsTableSeeder.php'),
                  __DIR__ . '/../database/seeds/TagCacheTableSeeder.php' => database_path('seeds/TagCacheTableSeeder.php'),
                  __DIR__ . '/../database/seeds/TagsTableSeeder.php' => database_path('seeds/TagsTableSeeder.php'),              
              ],'test-seeds');
          }
        }

    }
}
