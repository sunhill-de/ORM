<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalcclassesCalcCalcfieldTableSeeder extends Seeder {
	
    public function run() {
        DB::table('calcclasses_calc_calcfield')->truncate();
        DB::table('calcclasses_calc_calcfield')->insert([
            ['id'=>26,'value'=>'ABC'],
        ]);
    }

}