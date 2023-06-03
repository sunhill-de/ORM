<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributes')->truncate();
	    DB::table('attributes')->insert([
	        ['id'=>1,'name'=>'int_attribute','type'=>'integer','allowed_classes'=>"|dummy|"],
	        ['id'=>2,'name'=>'attribute1','type'=>'integer','allowed_classes'=>"|testparent|"],
	        ['id'=>3,'name'=>'attribute2','type'=>'integer','allowed_classes'=>"|testparent|"],
	        ['id'=>4,'name'=>'general_attribute','type'=>'integer','allowed_classes'=>"|object|"],
	        ['id'=>5,'name'=>'char_attribute','type'=>'string','allowed_classes'=>"|dummy|"],
	        ['id'=>6,'name'=>'float_attribute','type'=>'float','allowed_classes'=>"|dummy|"],
	        ['id'=>7,'name'=>'text_attribute','type'=>'text','allowed_classes'=>"|dummy|"],
	        ['id'=>8,'name'=>'child_attribute','type'=>'integer','allowed_classes'=>"|dummychild|"],
	        ['id'=>9,'name'=>'empty','type'=>'integer','allowed_classes'=>"|dummychild|"],
	    ]);
	}
}