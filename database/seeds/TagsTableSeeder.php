<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder {
	
	public function run() {
		DB::table('tags')->insert([
			['id'=>1,'parent_id'=>0,'options'=>1,'name'=>'TagA'],
			['id'=>2,'parent_id'=>1,'options'=>1,'name'=>'TagChildA'],
			['id'=>3,'parent_id'=>0,'options'=>0,'name'=>'TagB'],
			['id'=>4,'parent_id'=>3,'options'=>1,'name'=>'TagChildB'],
			['id'=>5,'parent_id'=>4,'options'=>1,'name'=>'TagChildB'],				
		]);
	}
}