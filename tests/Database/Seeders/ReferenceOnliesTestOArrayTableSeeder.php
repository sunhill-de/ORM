<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceOnliesTestOArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('referenceonlies_testoarray')->truncate();
        DB::table('referenceonlies_testoarray')->insert([
            ['id'=>27,'value'=>2,'index'=>0],
            ['id'=>27,'value'=>3,'index'=>1],
            ['id'=>28,'value'=>2,'index'=>0],
            ['id'=>28,'value'=>27,'index'=>1],
        ]);
    }

}