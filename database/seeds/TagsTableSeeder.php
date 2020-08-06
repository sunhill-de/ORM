<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder {
	
	public function run() {
		DB::table('tags')->insert([
		    ['id'=>1,'name'=>'TagA',0],
		    ['id'=>2,'name'=>'TagB',0],
		    ['id'=>3,'name'=>'TagC',2],
		    ['id'=>4,'name'=>'TagD',0],
		    ['id'=>5,'name'=>'TagE',0],
		    ['id'=>6,'name'=>'TagF',0],		    
		]);
	}
}