<?php
namespace Sunhill\ORM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimplePassthrusTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('passthrus')->truncate();
	    DB::table('passthrus')->insert([
                    [
		                'id'=>7,
                    ]
		]);
	}
}