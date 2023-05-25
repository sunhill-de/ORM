<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestParentParentSArrayTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testparents_parentsarray')->truncate();
        DB::table('testparents_parentsarray')->insert([
            ['id'=>9,'value'=>'String A','index'=>0],
            ['id'=>9,'value'=>'String B','index'=>1],
            
            ['id'=>10,'value'=>'ABCD','index'=>0],
            ['id'=>10,'value'=>'DEFG','index'=>1],
            ['id'=>10,'value'=>'HIJK','index'=>2],
            
            ['id'=>11,'value'=>'AA','index'=>0],
            ['id'=>11,'value'=>'BB','index'=>1],
            ['id'=>11,'value'=>'CC','index'=>2],
                        
            ['id'=>13,'value'=>'ABCD','index'=>0],
            ['id'=>13,'value'=>'XYZA','index'=>1],
            ['id'=>13,'value'=>'GGGG','index'=>2],
            
            ['id'=>14,'value'=>'DEFG','index'=>0],
            
            ['id'=>17,'value'=>'ABCDEFG','index'=>0],
            ['id'=>17,'value'=>'HIJKLMN','index'=>1],
            
            ['id'=>18,'value'=>'Something','index'=>0],
            ['id'=>18,'value'=>'Something else','index'=>1],
            ['id'=>18,'value'=>'Another something','index'=>2],
            
            ['id'=>19,'value'=>'HALLO','index'=>0],
            ['id'=>19,'value'=>'HELLO','index'=>1],
            ['id'=>19,'value'=>'HOLA','index'=>2],
                        
            ['id'=>22,'value'=>'ZZZZ','index'=>0],
            ['id'=>22,'value'=>'Iron Maiden','index'=>1],
            ['id'=>22,'value'=>'Muse','index'=>2],                                    
        ]);
    }

}