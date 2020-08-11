<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleObjectsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objects')->truncate();
	    DB::table('objects')->insert([
		    ['id'=>1,'classname'=>"\\Sunhill\\Test\\ts_dummy"],
		    ['id'=>2,'classname'=>"\\Sunhill\\Test\\ts_dummy"],
		    ['id'=>3,'classname'=>"\\Sunhill\\Test\\ts_dummy"],
		    ['id'=>4,'classname'=>"\\Sunhill\\Test\\ts_dummy"],
		    ['id'=>5,'classname'=>"\\Sunhill\\Test\\ts_testparent"],
		    ['id'=>6,'classname'=>"\\Sunhill\\Test\\ts_testchild"],
		    ['id'=>7,'classname'=>"\\Sunhill\\Test\\ts_passthru"],
		]);
	}
}