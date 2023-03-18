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
	    ]);
	}
}