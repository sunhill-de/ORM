<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleObjectsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objects')->truncate();
	    DB::table('objects')->insert([
	        ['id'=>1,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>2,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>3,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>4,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>5,'classname'=>"\\Sunhill\\ORM\\Test\\ts_testparent",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>6,'classname'=>"\\Sunhill\\ORM\\Test\\ts_testchild",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>7,'classname'=>"\\Sunhill\\ORM\\Test\\ts_passthru",'created_at'=>'2019-05-15 10:00:00'],
		]);
	}
}