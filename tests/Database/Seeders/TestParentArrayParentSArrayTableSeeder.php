<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestParentArrayParentSArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testparents_array_parentsarray')->truncate();
        DB::table('testparents_array_parentsarray')->insert([
            ['id'=>9,'target'=>'String A','index'=>0],
            ['id'=>9,'target'=>'String B','index'=>1],
            
            ['id'=>10,'target'=>'ABCD','index'=>0],
            ['id'=>10,'target'=>'DEFG','index'=>1],
            ['id'=>10,'target'=>'HIJK','index'=>2],
            
            ['id'=>11,'target'=>'AA','index'=>0],
            ['id'=>11,'target'=>'BB','index'=>1],
            ['id'=>11,'target'=>'CC','index'=>2],
                        
            ['id'=>13,'target'=>'ABCD','index'=>0],
            ['id'=>13,'target'=>'XYZA','index'=>1],
            ['id'=>13,'target'=>'GGGG','index'=>2],
            
            ['id'=>14,'target'=>'DEFG','index'=>0],
            
            ['id'=>17,'target'=>'ABCDEFG','index'=>0],
            ['id'=>17,'target'=>'HIJKLMN','index'=>1],
            
            ['id'=>18,'target'=>'Something','index'=>0],
            ['id'=>18,'target'=>'Something else','index'=>1],
            ['id'=>18,'target'=>'Another something','index'=>2],
            
            ['id'=>19,'target'=>'HALLO','index'=>0],
            ['id'=>19,'target'=>'HELLO','index'=>1],
            ['id'=>19,'target'=>'HOLA','index'=>2],
                        
            ['id'=>22,'target'=>'ZZZZ','index'=>0],
            ['id'=>22,'target'=>'Iron Maiden','index'=>1],
            ['id'=>22,'target'=>'Muse','index'=>2],                                    
        ]);
    }

}