<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttrCharAttributeTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attr_char_attribute')->truncate();
	    DB::table('attr_char_attribute')->insert([
	        ['object_id'=>5,'value'=>'This is a string'],
	    ]);
	}
}