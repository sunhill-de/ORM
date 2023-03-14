<?php
namespace Sunhill\ORM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['name'=>'int_attribute','type'=>'int','allowedobjects'=>"dummy",'property'=>''],
	        ['name'=>'attribute1','type'=>'int','allowedobjects'=>"testparent",'property'=>''],
	        ['name'=>'attribute2','type'=>'int','allowedobjects'=>"testparent",'property'=>''],
	        ['name'=>'general_attribute','type'=>'int','allowedobjects'=>"object",'property'=>''],
	        ['name'=>'char_attribute','type'=>'char','allowedobjects'=>"dummy",'property'=>''],
	        ['name'=>'float_attribute','type'=>'float','allowedobjects'=>"dummy",'property'=>''],
	        ['name'=>'text_attribute','type'=>'text','allowedobjects'=>"dummy",'property'=>''],
	    ]);
	}
}