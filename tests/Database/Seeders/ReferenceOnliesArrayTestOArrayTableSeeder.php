<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceOnliesArrayTestOArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('referenceonlies_array_testoarray')->truncate();
        DB::table('referenceonlies_array_testoarray')->insert([
            ['id'=>27,'target'=>2,'index'=>0],
            ['id'=>27,'target'=>3,'index'=>1],
            ['id'=>28,'target'=>2,'index'=>0],
            ['id'=>28,'target'=>27,'index'=>1],
        ]);
    }

}