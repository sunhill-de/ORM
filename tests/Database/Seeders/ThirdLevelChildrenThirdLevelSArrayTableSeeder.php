<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThirdLevelChildrenThirdLevelSArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('thirdlevelchildren_thirdlevelsarray')->truncate();
        DB::table('thirdlevelchildren_thirdlevelsarray')->insert([
            ['id'=>33,'value'=>'String A','index'=>0],
            ['id'=>33,'value'=>'String B','index'=>1],
        ]);
    }

}