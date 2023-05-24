<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyCollectionsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('dummycollections')->truncate();
	    DB::table('dummycollections')->insert([
		    ['id'=>1,'dummyint'=>123],
		    ['id'=>2,'dummyint'=>234],
		    ['id'=>3,'dummyint'=>123],
		    ['id'=>4,'dummyint'=>456],
	        ['id'=>5,'dummyint'=>123],
	        ['id'=>6,'dummyint'=>567],
	        ['id'=>7,'dummyint'=>678],
	        ['id'=>8,'dummyint'=>789],
	    ]);
	}
}