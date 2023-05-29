<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['id'=>1,'name'=>'int_attribute','type'=>'integer','allowedobjects'=>"dummy"],
	        ['id'=>2,'name'=>'attribute1','type'=>'integer','allowedobjects'=>"testparent"],
	        ['id'=>3,'name'=>'attribute2','type'=>'integer','allowedobjects'=>"testparent"],
	        ['id'=>4,'name'=>'general_attribute','type'=>'integer','allowedobjects'=>"object"],
	        ['id'=>5,'name'=>'char_attribute','type'=>'string','allowedobjects'=>"dummy"],
	        ['id'=>6,'name'=>'float_attribute','type'=>'float','allowedobjects'=>"dummy"],
	        ['id'=>7,'name'=>'text_attribute','type'=>'text','allowedobjects'=>"dummy"],
	        ['id'=>8,'name'=>'child_attribute','type'=>'integer','allowedobjects'=>"dummychild"],
	    ]);
	}
}