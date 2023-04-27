<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalcclassesCalcCalcfield2TableSeeder extends Seeder {
	
    public function run() {
        DB::table('calcclasses_calc_calcfield2')->truncate();
        DB::table('calcclasses_calc_calcfield2')->insert([
            ['id'=>26,'value'=>'ABC2'],
        ]);
    }

}