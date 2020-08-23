<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['name'=>'int_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\Test\\ts_dummy",'property'=>''],
	        ['name'=>'attribute1','type'=>'int','allowedobjects'=>"\\Sunhill\\Test\\ts_testparent",'property'=>''],
	        ['name'=>'attribute2','type'=>'int','allowedobjects'=>"\\Sunhill\\Test\\ts_testparent",'property'=>''],
	        ['name'=>'general_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\Objects\\oo_object",'property'=>''],
		]);
	}
}