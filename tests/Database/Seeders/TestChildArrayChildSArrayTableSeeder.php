<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestChildArrayChildSArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testchildren_array_childsarray')->truncate();
        DB::table('testchildren_array_childsarray')->insert([
            ['id'=>17,'value'=>'OPQRSTU','index'=>0],
            ['id'=>17,'value'=>'VXYZABC','index'=>1],
            ['id'=>18,'value'=>'Yea','index'=>0],            
            ['id'=>18,'value'=>'Yupp','index'=>1],
            ['id'=>20,'value'=>'ABCD','index'=>0],
            ['id'=>20,'value'=>'GGGG','index'=>1],            
            ['id'=>24,'value'=>'Only entry','index'=>0],
        ]);
    }

}