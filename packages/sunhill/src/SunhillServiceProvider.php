
<?php

namespace Sunhill;

use Illuminate\Support\ServiceProvider;

class SunhillServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }
    
    public function boot()
    {
    if ($this->app->runningInConsole()) {

      if (! class_exists('CreateAttributesTable')) {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_attributes_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_attributes_table.php'),,
            __DIR__ . '/../database/migrations/create_attributevalues_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_attributevalues_table.php'),,
            __DIR__ . '/../database/migrations/create_caching_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_caching_table.php'),,
            __DIR__ . '/../database/migrations/create_externalhooks_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_externalhooks_table.php'),,
            __DIR__ . '/../database/migrations/create_objectobjectassigns_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_objectobjectassigns_table.php'),,
            __DIR__ . '/../database/migrations/create_objects_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_objects_table.php'),,
            __DIR__ . '/../database/migrations/create_stringobjectassigns_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_stringobjectassigns_table.php'),,
            __DIR__ . '/../database/migrations/create_tagcache_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tagcache_table.php'),,
            __DIR__ . '/../database/migrations/create_tagobjectassigns_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tagobjectassigns_table.php'),,
            __DIR__ . '/../database/migrations/create_tags_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tags_table.php'),,            
        ], 'migrations');
      }
    }

    }
}
