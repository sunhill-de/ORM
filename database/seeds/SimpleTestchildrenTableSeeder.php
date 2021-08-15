<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleTestchildrenTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('testchildren')->truncate();
	    DB::table('testchildren')->insert([
            [
                'id'=>6,
	            'childint'=>345,
	            'childchar'=>'GHI',
	            'childfloat'=>3.45,
	            'childtext'=>'Norem Torem',
	            'childdatetime'=>'1973-01-24 18:00:00',
	            'childdate'=>'2016-06-17',
	            'childtime'=>'18:00:00',
	            'childenum'=>'testA'
	        ],
	   ]);
	}
}