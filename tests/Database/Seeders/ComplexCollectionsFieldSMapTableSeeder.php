<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplexCollectionsFieldSMapTableSeeder extends Seeder {
	
    public function run() {
        DB::table('complexcollections_field_smap')->truncate();
        DB::table('complexcollections_field_smap')->insert([
            ['id'=>9,'value'=>'ValueA','index'=>'KeyA'],
            ['id'=>9,'value'=>'ValueB','index'=>'KeyB'],
            
            ['id'=>10,'value'=>'ABCD','index'=>'KeyA'],
            ['id'=>10,'value'=>'DEFG','index'=>'KeyB'],
            ['id'=>10,'value'=>'HIJK','index'=>'KeyC'],
            
            ['id'=>11,'value'=>'AA','index'=>'KeyA'],
            ['id'=>11,'value'=>'BB','index'=>'KeyB'],
            ['id'=>11,'value'=>'CC','index'=>'KeyC'],
                        
            ['id'=>13,'value'=>'ABCD','index'=>'KeyA'],
            ['id'=>13,'value'=>'XYZA','index'=>'KeyB'],
            ['id'=>13,'value'=>'GGGG','index'=>'KeyC'],
            
            ['id'=>14,'value'=>'DEFG','index'=>'KeyA'],
            
            ['id'=>17,'value'=>'ABCDEFG','index'=>'KeyA'],
            ['id'=>17,'value'=>'HIJKLMN','index'=>'KeyB'],
            
            ['id'=>18,'value'=>'Something','index'=>'KeyA'],
            ['id'=>18,'value'=>'Something else','index'=>'KeyB'],
            ['id'=>18,'value'=>'Another something','index'=>'KeyC'],
            
            ['id'=>19,'value'=>'HALLO','index'=>'KeyA'],
            ['id'=>19,'value'=>'HELLO','index'=>'KeyB'],
            ['id'=>19,'value'=>'HOLA','index'=>'KeyC'],
                        
            ['id'=>22,'value'=>'ZZZZ','index'=>'KeyA'],
            ['id'=>22,'value'=>'Iron Maiden','index'=>'KeyB'],
            ['id'=>22,'value'=>'Muse','index'=>'KeyC'],                                    
        ]);
    }

}