<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceOnliesArrayTestSArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('referenceonlies_array_testsarray')->truncate();
        DB::table('referenceonlies_array_testsarray')->insert([
            ['id'=>27,'target'=>'Test A','index'=>0],
            ['id'=>27,'target'=>'Test B','index'=>1],
            ['id'=>28,'target'=>'Test B','index'=>0],
            ['id'=>28,'target'=>'Test C','index'=>1],
        ]);
    }

}