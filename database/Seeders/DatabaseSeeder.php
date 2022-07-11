<?php
namespace Sunhill\ORM\Database\Seeders;

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
        $this->call('TagsTableSeeder');
        $this->call('TagCacheTableSeeder');
    }
}
