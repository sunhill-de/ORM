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
	    
	        ['id'=>9,'classname'=>"testparent", 'uuid'=>'i123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>10,'classname'=>"testparent",'uuid'=>'k123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>11,'classname'=>"testparent",'uuid'=>'l123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>12,'classname'=>"testparent",'uuid'=>'m123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>13,'classname'=>"testparent",'uuid'=>'n123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>14,'classname'=>"testparent",'uuid'=>'o123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>15,'classname'=>"testparent",'uuid'=>'p123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>16,'classname'=>"testparent",'uuid'=>'q123','created_at'=>'2019-05-15 10:00:00'],

	        ['id'=>17,'classname'=>"testchild",'uuid'=>'r123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>18,'classname'=>"testchild",'uuid'=>'s123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>19,'classname'=>"testchild",'uuid'=>'t123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>20,'classname'=>"testchild",'uuid'=>'u123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>21,'classname'=>"testchild",'uuid'=>'v123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>22,'classname'=>"testchild",'uuid'=>'w123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>23,'classname'=>"testchild",'uuid'=>'x123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>24,'classname'=>"testchild",'uuid'=>'y123','created_at'=>'2019-05-15 10:00:00'],
	        
	        ['id'=>25,'classname'=>"testsimplechild",'uuid'=>'z123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>26,'classname'=>"testsimplechild",'uuid'=>'aa123','created_at'=>'2019-05-15 10:00:00'],

	        ['id'=>27,'classname'=>"referenceonly",'uuid'=>'bb123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>28,'classname'=>"referenceonly",'uuid'=>'cc123','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>29,'classname'=>"referenceonly",'uuid'=>'dd23','created_at'=>'2019-05-15 10:00:00'],
	        ['id'=>30,'classname'=>"referenceonly",'uuid'=>'ee123','created_at'=>'2019-05-15 10:00:00'],
	        
	        ['id'=>31,'classname'=>'calcclass','uuid'=>'ff123','created_at'=>'2019-05-15 10:00:00'],
	    ]);
	}
}