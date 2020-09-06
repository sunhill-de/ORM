<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchCachingTableSeeder extends Seeder {
	
    public function run() {
        DB::table('caching')->truncate();
        DB::table('caching')->insert([
            ['id'=>1,'object_id'=>5,'fieldname'=>'Acalc','value'=>'111=ABC'],
            ['id'=>2,'object_id'=>6,'fieldname'=>'Acalc','value'=>'222=ADE'],            
            ['id'=>3,'object_id'=>7,'fieldname'=>'Acalc','value'=>'333=BCC'],
            ['id'=>4,'object_id'=>8,'fieldname'=>'Acalc','value'=>'990=XYZ'],
            ['id'=>5,'object_id'=>9,'fieldname'=>'Acalc','value'=>'999=XCX'],
            ['id'=>6,'object_id'=>10,'fieldname'=>'Acalc','value'=>'500=GGG'],
            ['id'=>7,'object_id'=>11,'fieldname'=>'Acalc','value'=>'501=GGF'],
            ['id'=>8,'object_id'=>12,'fieldname'=>'Acalc','value'=>'502=GGT'],
            ['id'=>9,'object_id'=>13,'fieldname'=>'Acalc','value'=>'502=GGZ'],
            ['id'=>10,'object_id'=>14,'fieldname'=>'Acalc','value'=>'503=GTG'],
            ['id'=>11,'object_id'=>15,'fieldname'=>'Acalc','value'=>'503=GGG'],
            ['id'=>12,'object_id'=>10,'fieldname'=>'Bcalc','value'=>'111=ABC'],
            ['id'=>13,'object_id'=>11,'fieldname'=>'Bcalc','value'=>'601=BBB'],
            ['id'=>14,'object_id'=>12,'fieldname'=>'Bcalc','value'=>'602=CCC'],
            ['id'=>15,'object_id'=>13,'fieldname'=>'Bcalc','value'=>'602=DDC'],
            ['id'=>16,'object_id'=>14,'fieldname'=>'Bcalc','value'=>'603=ADD'],
            ['id'=>17,'object_id'=>15,'fieldname'=>'Bcalc','value'=>'603=GGG'],
        ]);
    }

}