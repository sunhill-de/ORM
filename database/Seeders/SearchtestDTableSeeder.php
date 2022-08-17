<?php
namespace Sunhill\ORM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchtestDTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('searchtestD')->truncate();
	    DB::table('searchtestD')->insert([
	        ['id'=>16,'Dchar'=>'ABC'],
	        ['id'=>17,'Dchar'=>'XXX'],
		]);
	}
}