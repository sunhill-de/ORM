<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleDummiesTableSeeder extends Seeder {
	
	public function run() {
		DB::table('dummies')->insert([
		    ['id'=>1,'dummyint'=>123],
		    ['id'=>2,'dummyint'=>234],
		    ['id'=>3,'dummyint'=>345],
		    ['id'=>4,'dummyint'=>456],
		]);
	}
}