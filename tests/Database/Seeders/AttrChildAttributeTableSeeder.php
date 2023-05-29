<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttrChildAttributeTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('attr_child_attribute')->truncate();
	    DB::table('attr_child_attribute')->insert([
	        ['object_id'=>8,'value'=>'999'],
	    ]);
	}
}