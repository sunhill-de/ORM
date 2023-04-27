<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestParentCalcParentCalcTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testparents_calc_parentcalc')->truncate();
        DB::table('testparents_calc_parentcalc')->insert([
            ['id'=>9, 'value'=>'111A'],
            ['id'=>10,'value'=>'123A'],
            ['id'=>11,'value'=>'222A'],
            ['id'=>12,'value'=>'123A'],
            ['id'=>13,'value'=>'234A'],
            ['id'=>14,'value'=>'555A'],
            ['id'=>15,'value'=>'432A'],
            ['id'=>16,'value'=>'700A'],
            ['id'=>17,'value'=>'123A'],
            ['id'=>18,'value'=>'800A'],
            ['id'=>19,'value'=>'900A'],
            ['id'=>20,'value'=>'666A'],
            ['id'=>21,'value'=>'580A'],
            ['id'=>22,'value'=>'432A'],
            ['id'=>23,'value'=>'345A'],
            ['id'=>24,'value'=>'723A'],
            ['id'=>25,'value'=>'999A'],
            ['id'=>26,'value'=>'123A'],
        ]);
    }

}