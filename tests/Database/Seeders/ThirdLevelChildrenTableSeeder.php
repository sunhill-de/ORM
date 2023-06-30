<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThirdLevelChildrenTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('thirdlevelchildren')->truncate();
	    DB::table('thirdlevelchildren')->insert([
	        ['id'=>33,'childchildint'=>55,'childchildchar'=>'ADAC','thirdlevelobject'=>1],	        
	    ]);
	}
}