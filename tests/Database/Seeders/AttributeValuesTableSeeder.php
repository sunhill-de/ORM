<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeValuesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributevalues')->truncate();
	    DB::table('attributevalues')->insert([
	        ['attribute_id'=>1,'object_id'=>4,'value'=>'5','textvalue'=>''],
	        ['attribute_id'=>1,'object_id'=>8,'value'=>'9','textvalue'=>''],	        

	        ['attribute_id'=>2,'object_id'=>9,'value'=>'123','textvalue'=>''],
	        ['attribute_id'=>3,'object_id'=>9,'value'=>'222','textvalue'=>''],
	        
	        ['attribute_id'=>3,'object_id'=>10,'value'=>'333','textvalue'=>''],

	        ['attribute_id'=>4,'object_id'=>1,'value'=>'444','textvalue'=>''],
	        ['attribute_id'=>4,'object_id'=>14,'value'=>'555','textvalue'=>''],

	        ['attribute_id'=>5,'object_id'=>5,'value'=>'This is a string','textvalue'=>''],
	        ['attribute_id'=>6,'object_id'=>5,'value'=>'1.23','textvalue'=>''],	       
	        ['attribute_id'=>7,'object_id'=>5,'value'=>'','textvalue'=>'This is a text'],

	        ['attribute_id'=>8,'object_id'=>8,'value'=>'999','textvalue'=>''],
	        
	    ]);
	}
}