<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objectobjectassigns')->truncate();
	    DB::table('objectobjectassigns')->insert([
	        ['container_id'=>9,'element_id'=>1,'field'=>'parentobject','index'=>0],
	        ['container_id'=>9,'element_id'=>2,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>9,'element_id'=>3,'field'=>'parentoarray','index'=>1],
	        
	        ['container_id'=>10,'element_id'=>4,'field'=>'parentobject','index'=>0],
	        ['container_id'=>10,'element_id'=>3,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>10,'element_id'=>2,'field'=>'parentoarray','index'=>1],
	        ['container_id'=>10,'element_id'=>1,'field'=>'parentoarray','index'=>2],
	        	               
	        ['container_id'=>11,'element_id'=>5,'field'=>'parentobject','index'=>0],
	        ['container_id'=>11,'element_id'=>6,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>11,'element_id'=>7,'field'=>'parentoarray','index'=>1],
	        ['container_id'=>11,'element_id'=>2,'field'=>'parentoarray','index'=>2],
	        
	        ['container_id'=>12,'element_id'=>4,'field'=>'parentobject','index'=>0],
	        
    	    ['container_id'=>13,'element_id'=>1,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>13,'element_id'=>2,'field'=>'parentoarray','index'=>1],
	        ['container_id'=>13,'element_id'=>3,'field'=>'parentoarray','index'=>2],
	        
	        ['container_id'=>14,'element_id'=>1,'field'=>'parentoarray','index'=>0],
	        
	        ['container_id'=>17,'element_id'=>3,'field'=>'parentobject','index'=>0],
	        ['container_id'=>17,'element_id'=>4,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>17,'element_id'=>5,'field'=>'parentoarray','index'=>1],
	        ['container_id'=>17,'element_id'=>3,'field'=>'childobject','index'=>0],
	        ['container_id'=>17,'element_id'=>4,'field'=>'childoarray','index'=>0],
	        ['container_id'=>17,'element_id'=>5,'field'=>'childoarray','index'=>1],
	        
	        ['container_id'=>18,'element_id'=>4,'field'=>'parentobject','index'=>0],
	        ['container_id'=>18,'element_id'=>3,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>18,'element_id'=>2,'field'=>'parentoarray','index'=>1],
	        ['container_id'=>18,'element_id'=>1,'field'=>'parentoarray','index'=>2],
	        ['container_id'=>18,'element_id'=>4,'field'=>'childobject','index'=>0],
	        ['container_id'=>18,'element_id'=>5,'field'=>'childoarray','index'=>0],
	        ['container_id'=>18,'element_id'=>6,'field'=>'childoarray','index'=>1],
	        ['container_id'=>18,'element_id'=>7,'field'=>'childoarray','index'=>2],
	        	        
	        ['container_id'=>19,'element_id'=>5,'field'=>'parentobject','index'=>0],
	        ['container_id'=>19,'element_id'=>6,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>19,'element_id'=>7,'field'=>'parentoarray','index'=>1],
	        ['container_id'=>19,'element_id'=>2,'field'=>'parentoarray','index'=>2],
	        ['container_id'=>19,'element_id'=>3,'field'=>'childobject','index'=>0],
	        
	        ['container_id'=>20,'element_id'=>2,'field'=>'parentobject','index'=>0],
	        ['container_id'=>20,'element_id'=>1,'field'=>'childoarray','index'=>0],
	        ['container_id'=>20,'element_id'=>3,'field'=>'childoarray','index'=>1],
	        
	        ['container_id'=>22,'element_id'=>5,'field'=>'parentoarray','index'=>0],
	        ['container_id'=>22,'element_id'=>6,'field'=>'parentoarray','index'=>1],
	        ['container_id'=>22,'element_id'=>4,'field'=>'parentoarray','index'=>2],
	        
	        ['container_id'=>24,'element_id'=>1,'field'=>'childoarray','index'=>0],
	       
	        ['container_id'=>25,'element_id'=>2,'field'=>'childobject','index'=>0],
	        
	        ['container_id'=>27,'element_id'=>1,'field'=>'testobject','index'=>0],
	        ['container_id'=>27,'element_id'=>2,'field'=>'testoarray','index'=>0],
	        ['container_id'=>27,'element_id'=>3,'field'=>'testoarray','index'=>1],

	        ['container_id'=>28,'element_id'=>2,'field'=>'testoarray','index'=>0],
	        ['container_id'=>28,'element_id'=>3,'field'=>'testoarray','index'=>1],

	        ['container_id'=>29,'element_id'=>4,'field'=>'testobject','index'=>0],
	        
	    ]);
	}
}