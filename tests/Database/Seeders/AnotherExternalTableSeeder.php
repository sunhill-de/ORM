<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnotherExternalTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('external')->truncate();
	    DB::table('external')->insert([
		    ['id_field'=>'WWW','external_int'=>111,'external_string'=>'AAA'],
	        ['id_field'=>'ZZZ','external_int'=>222,'external_string'=>'AAB'],
	        ['id_field'=>'DEF','external_int'=>333,'external_string'=>'BBB'],
	        ['id_field'=>'WED','external_int'=>444,'external_string'=>'AAA'],
	        ['id_field'=>'ZOO','external_int'=>444,'external_string'=>'ABA'],
	        ['id_field'=>'ZOO','external_int'=>666,'external_string'=>'CCC'],
	        ['id_field'=>'ZOO','external_int'=>555,'external_string'=>'ZZZ'],
	    ]);
	}
}