<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StringObjectAssignsTableSeeder extends Seeder {
	
    public function run() {
        DB::table('stringobjectassigns')->truncate();
        DB::table('stringobjectassigns')->insert([
                        
            ['container_id'=>20,'element_id'=>'ABCD','field'=>'childsarray','index'=>0],
            ['container_id'=>20,'element_id'=>'','field'=>'childsarray','index'=>1],
                       
            ['container_id'=>24,'element_id'=>'','field'=>'childsarray','index'=>0],
                        
            ['container_id'=>27,'element_id'=>'Test A','field'=>'testsarray','index'=>0],
            ['container_id'=>27,'element_id'=>'Test B','field'=>'testsarray','index'=>1],
            
            ['container_id'=>28,'element_id'=>'Test B','field'=>'testsarray','index'=>0],
            ['container_id'=>28,'element_id'=>'Test C','field'=>'testsarray','index'=>1],
            
        ]);
    }

}