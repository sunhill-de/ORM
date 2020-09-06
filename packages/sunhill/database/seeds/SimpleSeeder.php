<?php

use Illuminate\Database\Seeder;

class SimpleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call('TagsTableSeeder');
        $this->call('TagCacheTableSeeder');
        $this->call('AttributesTableSeeder');
        $this->call('SimpleObjectsTableSeeder');
        $this->call('SimpleDummiesTableSeeder');
        $this->call('SimpleTestparentsTableSeeder');
        $this->call('SimpleTestchildrenTableSeeder');
        $this->call('SimplePassthrusTableSeeder');
        $this->call('SimpleTagObjectAssignsTableSeeder');
        $this->call('SimpleObjectObjectAssignsTableSeeder');
        $this->call('SimpleStringObjectAssignsTableSeeder');
        $this->call('SimpleCachingTableSeeder');
        $this->call('SimpleAttributeValuesTableSeeder');
        $this->call('ExternalhooksTableSeeder');
    }
}
