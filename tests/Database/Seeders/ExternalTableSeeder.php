<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExternalTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('external')->truncate();
	    DB::table('external')->insert([
		    ['id_field'=>1,'external_int'=>123,'external_string'=>'AAA'],
	        ['id_field'=>2,'external_int'=>234,'external_string'=>'AAB'],
	        ['id_field'=>3,'external_int'=>123,'external_string'=>'BBB'],
	        ['id_field'=>4,'external_int'=>456,'external_string'=>'AAA'],
	        ['id_field'=>5,'external_int'=>123,'external_string'=>'ABA'],
	        ['id_field'=>6,'external_int'=>567,'external_string'=>'CCC'],
	        ['id_field'=>7,'external_int'=>678,'external_string'=>'ZZZ'],
	        ['id_field'=>8,'external_int'=>789,'external_string'=>'BBB'],
	        ['id_field'=>9,'external_int'=>211,'external_string'=>'ODE'],
	        ['id_field'=>10,'external_int'=>572,'external_string'=>'DEC'],
	        ['id_field'=>11,'external_int'=>185,'external_string'=>'JWA'],
	        ['id_field'=>12,'external_int'=>834,'external_string'=>'POW'],
	        ['id_field'=>13,'external_int'=>345,'external_string'=>'SWD'],
	        ['id_field'=>22,'external_int'=>912,'external_string'=>'WED'],
	    ]);
	}
}