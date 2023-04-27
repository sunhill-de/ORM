<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestChildArrayChildOArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testchildren_array_childoarray')->truncate();
        DB::table('testchildren_array_childoarray')->insert([
            ['id'=>17,'target'=>3,'index'=>0],
            ['id'=>17,'target'=>4,'index'=>1],
            ['id'=>17,'target'=>5,'index'=>2],            
            ['id'=>18,'target'=>5,'index'=>0],
            ['id'=>18,'target'=>6,'index'=>1],
            ['id'=>18,'target'=>7,'index'=>2],            
            ['id'=>20,'target'=>1,'index'=>0],
            ['id'=>20,'target'=>3,'index'=>1],            
            ['id'=>24,'target'=>1,'index'=>0],
        ]);
    }

}