<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objectobjectassigns')->truncate();
	    DB::table('objectobjectassigns')->insert([
	        ['container_id'=>27,'element_id'=>1,'field'=>'testobject','index'=>0],
	        ['container_id'=>27,'element_id'=>2,'field'=>'testoarray','index'=>0],
	        ['container_id'=>27,'element_id'=>3,'field'=>'testoarray','index'=>1],

	        ['container_id'=>28,'element_id'=>2,'field'=>'testoarray','index'=>0],
	        ['container_id'=>28,'element_id'=>27,'field'=>'testoarray','index'=>1],

	        ['container_id'=>29,'element_id'=>4,'field'=>'testobject','index'=>0],
	        
	    ]);
	}
}