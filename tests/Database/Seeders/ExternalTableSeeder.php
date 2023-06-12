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
	    ]);
	}
}