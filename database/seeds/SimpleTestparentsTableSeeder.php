<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleTestparentsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('testparents')->truncate();
	    DB::table('testparents')->insert([
		            [
		                'id'=>5,
		                'parentint'=>123,
		                'parentchar'=>'ABC',
		                'parentfloat'=>1.23,
		                'parenttext'=>'Lorem ipsum',
		                'parentdatetime'=>'1974-09-15 17:45:00',
		                'parentdate'=>'1978-06-05',
		                'parenttime'=>'01:11:00',
		                'parentenum'=>'testC'		                
		            ],[
		                'id'=>6,
		                'parentint'=>234,
		                'parentchar'=>'DEF',
		                'parentfloat'=>2.34,
		                'parenttext'=>'Upsala Dupsala',
		                'parentdatetime'=>'1970-09-11 18:00:00',
		                'parentdate'=>'2013-11-24',
		                'parenttime'=>'16:00:00',
		                'parentenum'=>'testB'		                
		            ],
		            [
		                'id'=>7,
		                'parentint'=>321,
		                'parentchar'=>'FED',
		                'parentfloat'=>4.32,
		                'parenttext'=>'Ups Dup',
		                'parentdatetime'=>'1970-09-11 18:00:00',
		                'parentdate'=>'2013-11-24',
		                'parenttime'=>'16:00:00',
		                'parentenum'=>'testB'		                
		            ]
		        
		]);
	}
}