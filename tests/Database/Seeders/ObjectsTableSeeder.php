<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objects')->truncate();
	    DB::table('objects')->insert([
	        ['id'=>1,'classname'=>"dummy",'_uuid'=>'a123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>2,'classname'=>"dummy",'_uuid'=>'b123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>3,'classname'=>"dummy",'_uuid'=>'c123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>4,'classname'=>"dummy",'_uuid'=>'d123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        
	        ['id'=>5,'classname'=>"dummychild",'_uuid'=>'e123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>6,'classname'=>"dummychild",'_uuid'=>'f123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>7,'classname'=>"dummychild",'_uuid'=>'g123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>8,'classname'=>"dummychild",'_uuid'=>'h123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	    
	        ['id'=>9,'classname'=>"testparent", '_uuid'=>'i123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>10,'classname'=>"testparent",'_uuid'=>'k123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>11,'classname'=>"testparent",'_uuid'=>'l123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>12,'classname'=>"testparent",'_uuid'=>'m123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>13,'classname'=>"testparent",'_uuid'=>'n123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>14,'classname'=>"testparent",'_uuid'=>'o123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>15,'classname'=>"testparent",'_uuid'=>'p123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>16,'classname'=>"testparent",'_uuid'=>'q123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],

	        ['id'=>17,'classname'=>"testchild",'_uuid'=>'r123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>18,'classname'=>"testchild",'_uuid'=>'s123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>19,'classname'=>"testchild",'_uuid'=>'t123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>20,'classname'=>"testchild",'_uuid'=>'u123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>21,'classname'=>"testchild",'_uuid'=>'v123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>22,'classname'=>"testchild",'_uuid'=>'w123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>23,'classname'=>"testchild",'_uuid'=>'x123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>24,'classname'=>"testchild",'_uuid'=>'y123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        
	        ['id'=>25,'classname'=>"testsimplechild",'_uuid'=>'z123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>26,'classname'=>"testsimplechild",'_uuid'=>'aa123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],

	        ['id'=>27,'classname'=>"referenceonly",'_uuid'=>'bb123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>28,'classname'=>"referenceonly",'_uuid'=>'cc123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>29,'classname'=>"referenceonly",'_uuid'=>'dd23','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>30,'classname'=>"referenceonly",'_uuid'=>'ee123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        
	        ['id'=>31,'classname'=>'calcclass','_uuid'=>'ff123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],

	        ['id'=>32,'classname'=>'secondlevelchild','_uuid'=>'gg123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        ['id'=>33,'classname'=>'thirdlevelchild','_uuid'=>'ff123','_created_at'=>'2019-05-15 10:00:00','_updated_at'=>'2019-05-15 10:00:00'],
	        
	        
	    ]);
	}
}