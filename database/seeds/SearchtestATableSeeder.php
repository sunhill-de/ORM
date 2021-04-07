<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchtestATableSeeder extends Seeder {
	
	public function run() {
	    DB::table('searchtestA')->truncate();
	    DB::table('searchtestA')->insert([
	        ['id'=>5,'Aint'=>111,'Anosearch'=>1,'Achar'=>'ABC'],
	        ['id'=>6,'Aint'=>222,'Anosearch'=>1,'Achar'=>'ADE'],
	        ['id'=>7,'Aint'=>333,'Anosearch'=>1,'Achar'=>'BCC'],
	        ['id'=>8,'Aint'=>990,'Anosearch'=>1,'Achar'=>'XYZ'],
	        ['id'=>9,'Aint'=>999,'Anosearch'=>1,'Achar'=>'XCX'],
	        ['id'=>10,'Aint'=>500,'Anosearch'=>1,'Achar'=>'GGG'],
	        ['id'=>11,'Aint'=>501,'Anosearch'=>1,'Achar'=>'ABC'],
	        ['id'=>12,'Aint'=>502,'Anosearch'=>1,'Achar'=>'GGT'],
	        ['id'=>13,'Aint'=>502,'Anosearch'=>1,'Achar'=>'GGZ'],
	        ['id'=>14,'Aint'=>503,'Anosearch'=>1,'Achar'=>'GTG'],
	        ['id'=>15,'Aint'=>503,'Anosearch'=>1,'Achar'=>'GGG']
		]);
	}
}