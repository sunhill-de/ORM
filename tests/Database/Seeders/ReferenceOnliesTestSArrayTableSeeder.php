<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceOnliesTestSArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('referenceonlies_array_testsarray')->truncate();
        DB::table('referenceonlies_array_testsarray')->insert([
            ['id'=>27,'value'=>'Test A','index'=>0],
            ['id'=>27,'value'=>'Test B','index'=>1],
            ['id'=>28,'value'=>'Test B','index'=>0],
            ['id'=>28,'value'=>'Test C','index'=>1],
        ]);
    }

}