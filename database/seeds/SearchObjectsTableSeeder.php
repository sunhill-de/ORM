<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchObjectsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objects')->truncate();
	    DB::table('objects')->insert([
	        ['id'=>1,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>2,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>3,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>4,'classname'=>"\\Sunhill\\ORM\\Test\\ts_dummy",'created_at'=>'2019-05-15 10:00:00'],      
	        ['id'=>5,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestA",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>6,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestA",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>7,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestA",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>8,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestA",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>9,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestA",'created_at'=>'2019-05-15 10:00:00'],	        
	        ['id'=>10,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestB",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>11,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestB",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>12,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestB",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>13,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestB",'created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>14,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestB",'created_at'=>'2019-05-15 10:00:00'],	        
	        ['id'=>15,'classname'=>"\\Sunhill\\ORM\\Tests\\Feature\\searchtestC",'created_at'=>'2019-05-15 10:00:00'],
	    ]);
	}
}