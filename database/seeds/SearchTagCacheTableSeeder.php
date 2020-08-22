<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchTagCacheTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('tagcache')->truncate();
	    DB::table('tagcache')->insert([
		    ['id'=>1,'name'=>'TagA','tag_id'=>1],
		    ['id'=>2,'name'=>'TagB','tag_id'=>2],
		    ['id'=>3,'name'=>'TagC','tag_id'=>3],
		    ['id'=>4,'name'=>'TagB.TagC','tag_id'=>3],
		    ['id'=>5,'name'=>'TagD','tag_id'=>4],
		    ['id'=>6,'name'=>'TagE','tag_id'=>5],
		    ['id'=>7,'name'=>'TagF','tag_id'=>6],		
		]);
	}
}