<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSimpleChildrenTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('testsimplechildren')->truncate();
	    DB::table('testsimplechildren')->insert([
	        [
	            'id'=>25,
	        ],
	        [
	            'id'=>26,
	        ],
	    ]);
	}
}