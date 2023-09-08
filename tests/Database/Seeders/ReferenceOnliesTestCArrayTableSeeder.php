<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceOnliesTestCArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('referenceonlies_testcarray')->truncate();
        DB::table('referenceonlies_testcarray')->insert([
            ['id'=>27,'value'=>1,'index'=>0],
            ['id'=>27,'value'=>2,'index'=>1],
            ['id'=>28,'value'=>3,'index'=>0],
            ['id'=>28,'value'=>4,'index'=>1],
        ]);
    }

}