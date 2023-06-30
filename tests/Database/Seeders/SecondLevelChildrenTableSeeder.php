<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecondLevelChildrenTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('secondlevelchildren')->truncate();
	    DB::table('secondlevelchildren')->insert([
	        ['id'=>32,'childint'=>1],
	        ['id'=>33,'childint'=>55],	        
	    ]);
	}
}