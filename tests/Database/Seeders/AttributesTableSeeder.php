<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['id'=>1,'name'=>'int_attribute','type'=>'int','allowedobjects'=>"dummy",'property'=>''],
	        ['id'=>2,'name'=>'attribute1','type'=>'int','allowedobjects'=>"testparent",'property'=>''],
	        ['id'=>3,'name'=>'attribute2','type'=>'int','allowedobjects'=>"testparent",'property'=>''],
	        ['id'=>4,'name'=>'general_attribute','type'=>'int','allowedobjects'=>"object",'property'=>''],
	        ['id'=>5,'name'=>'char_attribute','type'=>'char','allowedobjects'=>"dummy",'property'=>''],
	        ['id'=>6,'name'=>'float_attribute','type'=>'float','allowedobjects'=>"dummy",'property'=>''],
	        ['id'=>7,'name'=>'text_attribute','type'=>'text','allowedobjects'=>"dummy",'property'=>''],
	        ['id'=>8,'name'=>'child_attribute','type'=>'int','allowedobjects'=>"dummychild",'property'=>''],
	    ]);
	}
}