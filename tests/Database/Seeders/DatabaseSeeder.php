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
        $this->call(DummiesTableSeeder::class);
        $this->call(DummychildrenTableSeeder::class);
    }
}
