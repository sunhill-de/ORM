<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestChildArrayChildSArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testchildren_array_childsarray')->truncate();
        DB::table('testchildren_array_childsarray')->insert([
            ['id'=>17,'target'=>'OPQRSTU','index'=>0],
            ['id'=>17,'target'=>'VXYZABC','index'=>1],
            ['id'=>18,'target'=>'Yea','index'=>0],            
            ['id'=>18,'target'=>'Yupp','index'=>1],
            ['id'=>20,'target'=>'ABCD','index'=>0],
            ['id'=>20,'target'=>'GGGG','index'=>1],            
            ['id'=>24,'target'=>'Only entry','index'=>0],
        ]);
    }

}