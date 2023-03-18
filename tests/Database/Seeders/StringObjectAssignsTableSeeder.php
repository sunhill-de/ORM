<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StringObjectAssignsTableSeeder extends Seeder {
	
    public function run() {
        DB::table('stringobjectassigns')->truncate();
        DB::table('stringobjectassigns')->insert([
            ['container_id'=>9,'element_id'=>'String A','field'=>'parentsarray','index'=>0],
            ['container_id'=>9,'element_id'=>'String B','field'=>'parentsarray','index'=>1],
            
            ['container_id'=>10,'element_id'=>'ABCD','field'=>'parentsarray','index'=>0],
            ['container_id'=>10,'element_id'=>'DEFG','field'=>'parentsarray','index'=>1],
            ['container_id'=>10,'element_id'=>'HIJK','field'=>'parentsarray','index'=>2],
            
            ['container_id'=>11,'element_id'=>'AA','field'=>'parentsarray','index'=>0],
            ['container_id'=>11,'element_id'=>'BB','field'=>'parentsarray','index'=>1],
            ['container_id'=>11,'element_id'=>'CC','field'=>'parentsarray','index'=>2],
                        
            ['container_id'=>13,'element_id'=>'ABCD','field'=>'parentsarray','index'=>0],
            ['container_id'=>13,'element_id'=>'XYZA','field'=>'parentsarray','index'=>1],
            ['container_id'=>13,'element_id'=>'GGGG','field'=>'parentsarray','index'=>2],
            
            ['container_id'=>14,'element_id'=>'DEFG','field'=>'parentsarray','index'=>0],
            
            ['container_id'=>17,'element_id'=>'ABCDEFG','field'=>'parentsarray','index'=>0],
            ['container_id'=>17,'element_id'=>'HIJKLMN','field'=>'parentsarray','index'=>1],
            ['container_id'=>17,'element_id'=>'OPQRSTU','field'=>'childsarray','index'=>0],
            ['container_id'=>17,'element_id'=>'VXYZABC','field'=>'childsarray','index'=>1],
            
            ['container_id'=>18,'element_id'=>'Something','field'=>'parentsarray','index'=>0],
            ['container_id'=>18,'element_id'=>'Something else','field'=>'parentsarray','index'=>1],
            ['container_id'=>18,'element_id'=>'Another something','field'=>'parentsarray','index'=>2],
            ['container_id'=>18,'element_id'=>'Yea','field'=>'childsarray','index'=>0],
            ['container_id'=>18,'element_id'=>'Yupp','field'=>'childsarray','index'=>1],
            ['container_id'=>18,'element_id'=>'Yo','field'=>'childsarray','index'=>2],
            
            ['container_id'=>19,'element_id'=>'HALLO','field'=>'parentsarray','index'=>0],
            ['container_id'=>19,'element_id'=>'HELLO','field'=>'parentsarray','index'=>1],
            ['container_id'=>19,'element_id'=>'HOLA','field'=>'parentsarray','index'=>2],
            
            ['container_id'=>20,'element_id'=>'ABCD','field'=>'childsarray','index'=>0],
            ['container_id'=>20,'element_id'=>'GGGG','field'=>'childsarray','index'=>1],
            
            ['container_id'=>22,'element_id'=>'ZZZZ','field'=>'parentsarray','index'=>0],
            ['container_id'=>22,'element_id'=>'Iron Maiden','field'=>'parentsarray','index'=>1],
            ['container_id'=>22,'element_id'=>'Muse','field'=>'parentsarray','index'=>2],
            
            ['container_id'=>24,'element_id'=>'Only entry','field'=>'childsarray','index'=>0],
                        
            ['container_id'=>27,'element_id'=>'Test A','field'=>'testsarray','index'=>0],
            ['container_id'=>27,'element_id'=>'Test B','field'=>'testsarray','index'=>1],
            
            ['container_id'=>28,'element_id'=>'Test B','field'=>'testsarray','index'=>0],
            ['container_id'=>28,'element_id'=>'Test C','field'=>'testsarray','index'=>1],
            
        ]);
    }

}