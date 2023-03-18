<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferenceOnliesTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('referenceonlies')->truncate();
	    DB::table('referenceonlies')->insert([
	        ['id'=>27],
	        ['id'=>28],
	        ['id'=>29],
	        ['id'=>30],	        
	    ]);
	}
}