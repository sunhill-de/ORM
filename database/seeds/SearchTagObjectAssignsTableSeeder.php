<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchTagObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('tagobjectassigns')->truncate();
	    DB::table('tagobjectassigns')->insert([
	        ['container_id'=>5,'tag_id'=>1],
	        ['container_id'=>5,'tag_id'=>2],
	        ['container_id'=>5,'tag_id'=>5],
	        ['container_id'=>6,'tag_id'=>1],
	        ['container_id'=>6,'tag_id'=>3],
	        ['container_id'=>6,'tag_id'=>6],
	    ]);
	}
}