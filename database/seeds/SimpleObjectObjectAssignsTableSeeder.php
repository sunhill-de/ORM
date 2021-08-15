<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleObjectObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objectobjectassigns')->truncate();
	    DB::table('objectobjectassigns')->insert([
                    [
		                'container_id'=>5,
                        'element_id'=>1,
                        'field'=>'parentobject',
                        'index'=>0
                    ],[
                        'container_id'=>5,
                        'element_id'=>2,
                        'field'=>'parentoarray',
                        'index'=>0
                    ],[
                        'container_id'=>5,
                        'element_id'=>3,
                        'field'=>'parentoarray',
                        'index'=>1
                    ],[
                        'container_id'=>6,
                        'element_id'=>3,
                        'field'=>'parentobject',
                        'index'=>0
                    ],[
                        'container_id'=>6,
                        'element_id'=>1,
                        'field'=>'parentoarray',
                        'index'=>0
                    ],[
                        'container_id'=>6,
                        'element_id'=>2,
                        'field'=>'parentoarray',
                        'index'=>1
                    ],[
                        'container_id'=>6,
                        'element_id'=>2,
                        'field'=>'childobject',
                        'index'=>0
	                ],[
	                   'container_id'=>6,
	                   'element_id'=>3,
	                   'field'=>'childoarray',
	                   'index'=>0
	                ],[
	                   'container_id'=>6,
	                   'element_id'=>4,
	                   'field'=>'childoarray',
	                   'index'=>1
	                ],[
	                   'container_id'=>6,
	                   'element_id'=>1,
	                   'field'=>'childoarray',
	                   'index'=>2
	               ]
	        
		]);
	}
}