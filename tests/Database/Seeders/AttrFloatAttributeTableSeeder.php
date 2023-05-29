<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttrFloatAttributeTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attr_float_attribute')->truncate();
	    DB::table('attr_float_attribute')->insert([
	        ['object_id'=>5,'value'=>'1.23'],
	    ]);
	}
}