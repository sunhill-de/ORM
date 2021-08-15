<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleCachingTableSeeder extends Seeder {
	
    public function run() {
        DB::table('caching')->truncate();
        DB::table('caching')->insert([
            [
                'id'=>1,
                'object_id'=>5,
                'fieldname'=>'parentcalc',
                'value'=>'123A'
            ],[
                'id'=>2,
                'object_id'=>6,
                'fieldname'=>'parentcalc',
                'value'=>'234A'
            ]            
        ]);
    }

}