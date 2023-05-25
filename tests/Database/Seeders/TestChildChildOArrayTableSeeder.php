<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestChildChildOArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testchildren_childoarray')->truncate();
        DB::table('testchildren_childoarray')->insert([
            ['id'=>17,'value'=>3,'index'=>0],
            ['id'=>17,'value'=>4,'index'=>1],
            ['id'=>17,'value'=>5,'index'=>2],            
            ['id'=>18,'value'=>5,'index'=>0],
            ['id'=>18,'value'=>6,'index'=>1],
            ['id'=>18,'value'=>7,'index'=>2],            
            ['id'=>20,'value'=>1,'index'=>0],
            ['id'=>20,'value'=>3,'index'=>1],            
            ['id'=>24,'value'=>1,'index'=>0],
        ]);
    }

}