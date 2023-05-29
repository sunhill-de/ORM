<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttrAttribute2TableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attr_attribute2')->truncate();
	    DB::table('attr_attribute2')->insert([
	        ['object_id'=>9,'value'=>'222'],
	        ['object_id'=>10,'value'=>'333'],
	        ['object_id'=>17,'value'=>'543'],
	    ]);
	}
}