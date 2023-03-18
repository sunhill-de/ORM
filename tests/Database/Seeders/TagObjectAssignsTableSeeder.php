<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagObjectAssignsTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('tagobjectassigns')->truncate();
	    DB::table('tagobjectassigns')->insert([
	        ['container_id'=>1,'tag_id'=>1],
	        ['container_id'=>1,'tag_id'=>2],
	        ['container_id'=>1,'tag_id'=>4],
	        
	        ['container_id'=>9,'tag_id'=>3],
	        ['container_id'=>9,'tag_id'=>4],
	        ['container_id'=>9,'tag_id'=>5],
	        
	        ['container_id'=>10,'tag_id'=>8],

	        ['container_id'=>11,'tag_id'=>8],

	        ['container_id'=>12,'tag_id'=>1],
	        ['container_id'=>12,'tag_id'=>3],
	        ['container_id'=>12,'tag_id'=>8],
	        

	        ['container_id'=>17,'tag_id'=>1],
	        ['container_id'=>17,'tag_id'=>2],
	        ['container_id'=>17,'tag_id'=>4],
	        
	        ['container_id'=>19,'tag_id'=>3],
	        ['container_id'=>19,'tag_id'=>4],
	        ['container_id'=>19,'tag_id'=>5],
	        
	        ['container_id'=>20,'tag_id'=>8],
	        
	        ['container_id'=>21,'tag_id'=>8],
	        
	        ['container_id'=>22,'tag_id'=>1],
	        ['container_id'=>22,'tag_id'=>3],
	        ['container_id'=>22,'tag_id'=>8],

	        ['container_id'=>27,'tag_id'=>1],
	        ['container_id'=>27,'tag_id'=>3],
	        ['container_id'=>27,'tag_id'=>8],
	        
	    ]);
	}
}