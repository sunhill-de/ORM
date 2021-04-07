<?php

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
        $this->call('SearchObjectsTableSeeder');
        $this->call('SearchDummiesTableSeeder');
        $this->call('SearchtestATableSeeder');
        $this->call('SearchtestBTableSeeder');
        $this->call('SearchtestCTableSeeder');
        $this->call('SearchCachingTableSeeder');
        $this->call('SearchTagsTableSeeder');
        $this->call('SearchTagCacheTableSeeder');
        $this->call('SearchTagObjectAssignsTableSeeder');        
        $this->call('SearchObjectObjectAssignsTableSeeder');
        $this->call('SearchStringObjectAssignsTableSeeder');
        
        /*$this->call('SearchAttributesTableSeeder');
        $this->call('SearchAttributeValuesTableSeeder');*/
    }
}
