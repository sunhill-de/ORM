<?php
namespace Sunhill\ORM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchtestCTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('searchtestC')->truncate();
	    DB::table('searchtestC')->insert([
	        ['id'=>15]
		]);
	}
}