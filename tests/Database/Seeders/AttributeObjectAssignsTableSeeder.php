<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attributeobjectassigns')->truncate();
	    DB::table('attributeobjectassigns')->insert([
	        ['attribute_id'=>1,'object_id'=>4],
	        ['attribute_id'=>1,'object_id'=>8],	        
	        ['attribute_id'=>2,'object_id'=>9],
	        ['attribute_id'=>2,'object_id'=>17],
	        
	        ['attribute_id'=>3,'object_id'=>9],	        
	        ['attribute_id'=>3,'object_id'=>10],
	        ['attribute_id'=>3,'object_id'=>17],
	        
	        ['attribute_id'=>4,'object_id'=>1],
	        ['attribute_id'=>4,'object_id'=>14],

	        ['attribute_id'=>5,'object_id'=>5],
	        ['attribute_id'=>6,'object_id'=>5],	       
	        ['attribute_id'=>7,'object_id'=>5],
	        ['attribute_id'=>8,'object_id'=>8],
	        
	    ]);
	}
}