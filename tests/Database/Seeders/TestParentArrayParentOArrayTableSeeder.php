<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestParentArrayParentOArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testparents_array_parentoarray')->truncate();
        DB::table('testparents_array_parentoarray')->insert([
            ['id'=>9,'target'=>2,'index'=>0],
            ['id'=>9,'target'=>3,'index'=>1],
            ['id'=>10,'target'=>3,'index'=>0],
            ['id'=>10,'target'=>2,'index'=>1],
            ['id'=>10,'target'=>1,'index'=>2],
            ['id'=>11,'target'=>6,'index'=>0],
            ['id'=>11,'target'=>7,'index'=>1],
            ['id'=>11,'target'=>2,'index'=>2],
            ['id'=>13,'target'=>1,'index'=>0],
            ['id'=>13,'target'=>2,'index'=>1],
            ['id'=>13,'target'=>3,'index'=>2],            
            ['id'=>14,'target'=>1,'index'=>0],            
            ['id'=>17,'target'=>4,'index'=>0],
            ['id'=>17,'target'=>5,'index'=>1],
            ['id'=>18,'target'=>3,'index'=>0],
            ['id'=>18,'target'=>2,'index'=>1],
            ['id'=>18,'target'=>1,'index'=>2],
            ['id'=>19,'target'=>6,'index'=>0],
            ['id'=>19,'target'=>7,'index'=>1],
            ['id'=>19,'target'=>2,'index'=>2],
            ['id'=>22,'target'=>5,'index'=>0],
            ['id'=>22,'target'=>6,'index'=>1],
            ['id'=>22,'target'=>4,'index'=>2],
        ]);
    }

}