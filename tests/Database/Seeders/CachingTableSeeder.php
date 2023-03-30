<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CachingTableSeeder extends Seeder {
	
    public function run() {
        DB::table('caching')->truncate();
        DB::table('caching')->insert([
            ['id'=>1, 'object_id'=>9, 'fieldname'=>'parentcalc','value'=>'111A'],
            ['id'=>2, 'object_id'=>10,'fieldname'=>'parentcalc','value'=>'123A'],            
            ['id'=>3, 'object_id'=>11,'fieldname'=>'parentcalc','value'=>'222A'],
            ['id'=>4, 'object_id'=>12,'fieldname'=>'parentcalc','value'=>'123A'],
            ['id'=>5, 'object_id'=>13,'fieldname'=>'parentcalc','value'=>'234A'],
            ['id'=>6, 'object_id'=>14,'fieldname'=>'parentcalc','value'=>'555A'],
            ['id'=>7, 'object_id'=>15,'fieldname'=>'parentcalc','value'=>'432A'],
            ['id'=>8, 'object_id'=>16,'fieldname'=>'parentcalc','value'=>'700A'],
            ['id'=>9, 'object_id'=>17,'fieldname'=>'parentcalc','value'=>'123A'],
            ['id'=>10,'object_id'=>18,'fieldname'=>'parentcalc','value'=>'800A'],
            ['id'=>11,'object_id'=>19,'fieldname'=>'parentcalc','value'=>'900A'],
            ['id'=>12,'object_id'=>20,'fieldname'=>'parentcalc','value'=>'666A'],
            ['id'=>13,'object_id'=>21,'fieldname'=>'parentcalc','value'=>'580A'],
            ['id'=>14,'object_id'=>22,'fieldname'=>'parentcalc','value'=>'432A'],
            ['id'=>15,'object_id'=>23,'fieldname'=>'parentcalc','value'=>'345A'],
            ['id'=>16,'object_id'=>24,'fieldname'=>'parentcalc','value'=>'723A'],
            ['id'=>17,'object_id'=>25,'fieldname'=>'parentcalc','value'=>'999A'],
            ['id'=>18,'object_id'=>26,'fieldname'=>'parentcalc','value'=>'123A'],            
            ['id'=>19,'object_id'=>17,'fieldname'=>'childcalc', 'value'=>'777B'],
            ['id'=>20,'object_id'=>18,'fieldname'=>'childcalc', 'value'=>'801B'],
            ['id'=>21,'object_id'=>19,'fieldname'=>'childcalc', 'value'=>'900B'],
            ['id'=>22,'object_id'=>20,'fieldname'=>'childcalc', 'value'=>'666B'],
            ['id'=>23,'object_id'=>21,'fieldname'=>'childcalc', 'value'=>'112B'],
            ['id'=>24,'object_id'=>22,'fieldname'=>'childcalc', 'value'=>'321B'],
            ['id'=>25,'object_id'=>23,'fieldname'=>'childcalc', 'value'=>'345B'],
            ['id'=>26,'object_id'=>24,'fieldname'=>'childcalc', 'value'=>'777B'],            
            ['id'=>27,'object_id'=>31,'fieldname'=>'calcfield', 'value'=>'ABC'],
        ]);
    }

}