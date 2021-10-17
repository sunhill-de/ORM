<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['name'=>'int_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",'property'=>''],
	        ['name'=>'attribute1','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Test\\TestParent",'property'=>''],
	        ['name'=>'attribute2','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Test\\TestParent",'property'=>''],
	        ['name'=>'general_attribute','type'=>'int','allowedobjects'=>"\\Sunhill\\ORM\\Objects\\ORMObject",'property'=>''],
	        ['name'=>'char_attribute','type'=>'char','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",'property'=>''],
	        ['name'=>'float_attribute','type'=>'float','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",'property'=>''],
	        ['name'=>'text_attribute','type'=>'text','allowedobjects'=>"\\Sunhill\\ORM\\Tests\\Objects\\Dummy",'property'=>''],
	    ]);
	}
}