<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('objectobjectassigns')->truncate();
	    DB::table('objectobjectassigns')->insert([
            ['container_id'=>9,'target_id'=>1],	        
	        ['container_id'=>9,'target_id'=>2],
	        ['container_id'=>9,'target_id'=>3],
	        
	        ['container_id'=>10,'target_id'=>4],
	        ['container_id'=>10,'target_id'=>3],
	        ['container_id'=>10,'target_id'=>2],
	        ['container_id'=>10,'target_id'=>1],
	        
	        ['container_id'=>11,'target_id'=>5],
	        ['container_id'=>11,'target_id'=>6],
	        ['container_id'=>11,'target_id'=>7],
	        ['container_id'=>11,'target_id'=>2],

	        ['container_id'=>12,'target_id'=>4],
	        
	        ['container_id'=>13,'target_id'=>1],
	        ['container_id'=>13,'target_id'=>2],
	        ['container_id'=>13,'target_id'=>3],

	        ['container_id'=>14,'target_id'=>1],
	        
	        ['container_id'=>17,'target_id'=>3],
	        ['container_id'=>17,'target_id'=>4],
	        ['container_id'=>17,'target_id'=>5],
	        
	        ['container_id'=>18,'target_id'=>4],
	        ['container_id'=>18,'target_id'=>3],
	        ['container_id'=>18,'target_id'=>2],
	        ['container_id'=>18,'target_id'=>1],
	        ['container_id'=>18,'target_id'=>5],
	        ['container_id'=>18,'target_id'=>6],
	        ['container_id'=>18,'target_id'=>7],
	        
	        ['container_id'=>19,'target_id'=>5],
	        ['container_id'=>19,'target_id'=>6],
	        ['container_id'=>19,'target_id'=>7],
	        ['container_id'=>19,'target_id'=>2],
	        ['container_id'=>19,'target_id'=>3],
	        
	        ['container_id'=>20,'target_id'=>2],
	        ['container_id'=>20,'target_id'=>1],
	        ['container_id'=>20,'target_id'=>3],
	        
	        ['container_id'=>22,'target_id'=>5],
	        ['container_id'=>22,'target_id'=>6],
	        ['container_id'=>22,'target_id'=>4],
	        
	        ['container_id'=>25,'target_id'=>2],

	        ['container_id'=>27,'target_id'=>2],
	        ['container_id'=>27,'target_id'=>3],
	        
	        ['container_id'=>28,'target_id'=>2],
	        ['container_id'=>28,'target_id'=>27],
	        
	        ['container_id'=>33,'target_id'=>1],
	        
	        ['container_id'=>34,'target_id'=>35],
	        ['container_id'=>35,'target_id'=>36],
	        ['container_id'=>35,'target_id'=>34],
	        ['container_id'=>36,'targer_id'=>35],
	        
	    ]);
	}
}