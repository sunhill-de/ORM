<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objects')->truncate();
	    DB::table('objects')->insert([
	        ['id'=>1,'classname'=>"dummy",'uuid'=>'a123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>2,'classname'=>"dummy",'uuid'=>'b123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>3,'classname'=>"dummy",'uuid'=>'c123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>4,'classname'=>"dummy",'uuid'=>'d123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>5,'classname'=>"dummychild",'uuid'=>'e123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>6,'classname'=>"dummychild",'uuid'=>'f123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>7,'classname'=>"dummychild",'uuid'=>'g123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>8,'classname'=>"dummychild",'uuid'=>'h123','created_at'=>'2019-05-15 10:00:00'],
	    ]);
	}
}