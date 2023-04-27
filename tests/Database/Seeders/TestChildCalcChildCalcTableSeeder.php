<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestChildCalcChildCalcTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testchildren_calc_childcalc')->truncate();
        DB::table('testchildren_calc_childcalc')->insert([
            ['id'=>17,'value'=>'777B'],
            ['id'=>18,'value'=>'801B'],
            ['id'=>19,'value'=>'900B'],
            ['id'=>20,'value'=>'666B'],
            ['id'=>21,'value'=>'112B'],
            ['id'=>22,'value'=>'321B'],
            ['id'=>23,'value'=>'345B'],
            ['id'=>24,'value'=>'777B'],
        ]);
    }

}