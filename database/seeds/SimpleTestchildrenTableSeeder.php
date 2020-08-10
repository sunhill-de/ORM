<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleTestchildrenTableSeeder extends Seeder {
	
	public function run() {
		DB::table('passthrus')->insert([
                    [
		                'id'=>7,
                    ]
		]);
	}
}