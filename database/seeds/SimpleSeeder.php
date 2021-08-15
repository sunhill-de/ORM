<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\TagsTableSeeder;

class SimpleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(TagsTableSeeder::class);
        $this->call(TagCacheTableSeeder::class);
        $this->call(AttributesTableSeeder::class);
        $this->call(SimpleObjectsTableSeeder::class);
        $this->call(SimpleDummiesTableSeeder::class);
        $this->call(SimpleTestparentsTableSeeder::class);
        $this->call(SimpleTestchildrenTableSeeder::class);
        $this->call(SimplePassthrusTableSeeder::class);
        $this->call(SimpleTagObjectAssignsTableSeeder::class);
        $this->call(SimpleObjectObjectAssignsTableSeeder::class);
        $this->call(SimpleStringObjectAssignsTableSeeder::class);
        $this->call(SimpleCachingTableSeeder::class);
        $this->call(SimpleAttributeValuesTableSeeder::class);
        $this->call(ExternalhooksTableSeeder::class);
    }
}
