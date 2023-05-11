<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestParentArrayParentOArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testparents_array_parentoarray')->truncate();
        DB::table('testparents_array_parentoarray')->insert([
            ['id'=>9,'value'=>2,'index'=>0],
            ['id'=>9,'value'=>3,'index'=>1],
            ['id'=>10,'value'=>3,'index'=>0],
            ['id'=>10,'value'=>2,'index'=>1],
            ['id'=>10,'value'=>1,'index'=>2],
            ['id'=>11,'value'=>6,'index'=>0],
            ['id'=>11,'value'=>7,'index'=>1],
            ['id'=>11,'value'=>2,'index'=>2],
            ['id'=>13,'value'=>1,'index'=>0],
            ['id'=>13,'value'=>2,'index'=>1],
            ['id'=>13,'value'=>3,'index'=>2],            
            ['id'=>14,'value'=>1,'index'=>0],            
            ['id'=>17,'value'=>4,'index'=>0],
            ['id'=>17,'value'=>5,'index'=>1],
            ['id'=>18,'value'=>3,'index'=>0],
            ['id'=>18,'value'=>2,'index'=>1],
            ['id'=>18,'value'=>1,'index'=>2],
            ['id'=>19,'value'=>6,'index'=>0],
            ['id'=>19,'value'=>7,'index'=>1],
            ['id'=>19,'value'=>2,'index'=>2],
            ['id'=>22,'value'=>5,'index'=>0],
            ['id'=>22,'value'=>6,'index'=>1],
            ['id'=>22,'value'=>4,'index'=>2],
        ]);
    }

}