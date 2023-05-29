<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttrIntAttributeTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attr_int_attribute')->truncate();
	    DB::table('attr_int_attribute')->insert([
	        ['object_id'=>4,'value'=>'5'],
	        ['object_id'=>8,'value'=>'9'],	        
	    ]);
	}
}