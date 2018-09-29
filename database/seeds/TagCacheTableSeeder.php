<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagCacheTableSeeder extends Seeder {
	
	public function run() {
		DB::table('tagcache')->insert([
				['id'=>1,'name'=>'TagA','tag_id'=>1],
				['id'=>2,'name'=>'TagChildA','tag_id'=>2],
				['id'=>3,'name'=>'TagA.TagChildA','tag_id'=>2],
				['id'=>4,'name'=>'TagB','tag_id'=>3],
				['id'=>5,'name'=>'TagChildB','tag_id'=>4],
				['id'=>6,'name'=>'TagChildB','tag_id'=>5],
				['id'=>7,'name'=>'TagB.TagChildB','tag_id'=>4],
				['id'=>8,'name'=>'TagB.TagChildB.TagChildB','tag_id'=>5],
				['id'=>9,'name'=>'TagChildB.TagChildB','tag_id'=>5]
		]);
	}
}