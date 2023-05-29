<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttrGeneralAttributeTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attr_general_attribute')->truncate();
	    DB::table('attr_general_attribute')->insert([
	        ['object_id'=>1,'value'=>'444'],
	        ['object_id'=>14,'value'=>'555'],
	    ]);
	}
}