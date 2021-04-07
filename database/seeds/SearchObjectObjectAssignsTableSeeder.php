<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchObjectObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objectobjectassigns')->truncate();
	    DB::table('objectobjectassigns')->insert([
	            ['container_id'=>7,'element_id'=>1,'field'=>'Aobject','index'=>0],
	            ['container_id'=>8,'element_id'=>2,'field'=>'Aobject','index'=>0],
	            ['container_id'=>13,'element_id'=>1,'field'=>'Aobject','index'=>0],
	            ['container_id'=>13,'element_id'=>1,'field'=>'Bobject','index'=>0],
	            ['container_id'=>9,'element_id'=>3,'field'=>'Aoarray','index'=>0],
	            ['container_id'=>9,'element_id'=>4,'field'=>'Aoarray','index'=>1],
	            ['container_id'=>13,'element_id'=>4,'field'=>'Boarray','index'=>0],
	            
		]);
	}
}