<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummychildrenTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('dummychildren')->truncate();
	    DB::table('dummychildren')->insert([
	        ['id'=>5,'dummychildint'=>123],
	        ['id'=>6,'dummychildint'=>890],
	        ['id'=>7,'dummychildint'=>901],
	        ['id'=>8,'dummychildint'=>999],
	    ]);
	}
}