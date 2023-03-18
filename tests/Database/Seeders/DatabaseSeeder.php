<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(ObjectsTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(TagCacheTableSeeder::class);
        $this->call(AttributesTableSeeder::class);
        $this->call(AttributeValuesTableSeeder::class);
        $this->call(CachingTableSeeder::class);
        $this->call(ObjectObjectAssignsTableSeeder::class);
        $this->call(StringObjectAssignsTableSeeder::class);
        $this->call(TagObjectAssignsTableSeeder::class);
        
        $this->call(DummiesTableSeeder::class);
        $this->call(DummychildrenTableSeeder::class);
        $this->call(TestParentsTableSeeder::class);
        $this->call(TestChildrenTableSeeder::class);
        $this->call(TestSimpleChildrenTableSeeder::class);
        $this->call(ReferenceOnliesTableSeeder::class);
        $this->call(SecondLevelChildrenTableSeeder::class);
    }
}
