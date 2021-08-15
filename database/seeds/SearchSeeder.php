<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(SearchObjectsTableSeeder::class);
        $this->call(SearchDummiesTableSeeder::class);
        $this->call(SearchtestATableSeeder::class);
        $this->call(SearchtestBTableSeeder::class);
        $this->call(SearchtestCTableSeeder::class);
        $this->call(SearchCachingTableSeeder::class);
        $this->call(SearchTagsTableSeeder::class);
        $this->call(SearchTagCacheTableSeeder::class);
        $this->call(SearchTagObjectAssignsTableSeeder::class);        
        $this->call(SearchObjectObjectAssignsTableSeeder::class);
        $this->call(SearchStringObjectAssignsTableSeeder::class);
        
        /*$this->call('SearchAttributesTableSeeder');
        $this->call('SearchAttributeValuesTableSeeder');*/
    }
}
