<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttrAttribute1TableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attr_attribute1')->truncate();
	    DB::table('attr_attribute1')->insert([
	        ['object_id'=>9,'value'=>'123'],
	        ['object_id'=>17,'value'=>'654'],
	    ]);
	}
}