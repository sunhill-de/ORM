<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CircularsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('circulars')->truncate();
	    DB::table('circulars')->insert([
	        ['id'=>34,'payload'=>111,'parent'=>null,'child'=>35],
	        ['id'=>35,'payload'=>222,'parent'=>34,'child'=>36],
	        ['id'=>36,'payload'=>333,'parent'=>35,'child'=>null],
	    ]);
	}
}