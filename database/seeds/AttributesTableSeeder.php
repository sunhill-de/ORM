<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['id'=>1,'name'=>'int_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\Test\\ts_dummy",'property'=>''],
	        ['id'=>2,'name'=>'attribute1','type'=>'int','allowedobjects'=>"\\Sunhill\\Test\\ts_testparent",'property'=>''],
	        ['id'=>3,'name'=>'attribute2','type'=>'int','allowedobjects'=>"\\Sunhill\\Test\\ts_testparent",'property'=>''],
	        ['id'=>4,'name'=>'general_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\Objects\\oo_object",'property'=>''],
		]);
	}
}