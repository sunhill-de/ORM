<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['name'=>'int_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\ts_dummy",'property'=>''],
	        ['name'=>'attribute1','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Test\\ts_testparent",'property'=>''],
	        ['name'=>'attribute2','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Test\\ts_testparent",'property'=>''],
	        ['name'=>'general_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Objects\\ORMObject",'property'=>''],
	        ['name'=>'char_attribute','type'=>'char','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\ts_dummy",'property'=>''],
	        ['name'=>'float_attribute','type'=>'float','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\ts_dummy",'property'=>''],
	        ['name'=>'text_attribute','type'=>'text','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\ts_dummy",'property'=>''],
	    ]);
	}
}