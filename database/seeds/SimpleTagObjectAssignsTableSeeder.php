<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleTagObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('tagobjectassigns')->insert([
                    [
		                'container_id'=>1,
                        'tag_id'=>1
                    ],[
                        'container_id'=>1,
                        'tag_id'=>2                        
                    ]
		]);
	}
}