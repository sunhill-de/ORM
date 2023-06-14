<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestParentParentMapTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testparents_parentmap')->truncate();
        DB::table('testparents_parentmap')->insert([
            ['id'=>9,'value'=>'Value A','index'=>'KeyA'],
            ['id'=>9,'value'=>'Value B','index'=>'KeyB'],
            
            ['id'=>10,'value'=>'Tessa','index'=>'KeyA'],
            ['id'=>10,'value'=>'Johanna','index'=>'KeyC'],
            ['id'=>10,'value'=>'Emily','index'=>'KeyB'],
            
            ['id'=>11,'value'=>'Lina','index'=>'KeyA'],
            ['id'=>11,'value'=>'Ricarda','index'=>'KeyB'],
            ['id'=>11,'value'=>'Käthe','index'=>'KeyD'],
                        
            ['id'=>13,'value'=>'Störtebecker','index'=>'KeyD'],
            ['id'=>13,'value'=>'Jever','index'=>'KeyE'],
            ['id'=>13,'value'=>'Becks','index'=>'KeyF'],
            
            ['id'=>14,'value'=>'PPPP','index'=>'KeyA'],
            
            ['id'=>17,'value'=>'ABC','index'=>'KeyA'],
            ['id'=>17,'value'=>'DEF','index'=>'KeyC'],
            
            ['id'=>18,'value'=>'Lina','index'=>'KeyA'],
            ['id'=>18,'value'=>'Ricarda','index'=>'KeyC'],
            ['id'=>18,'value'=>'Käthe','index'=>'KeyE'],
            
            ['id'=>19,'value'=>'PS1','index'=>'KeyA'],
            ['id'=>19,'value'=>'PS2','index'=>'KeyB'],
            ['id'=>19,'value'=>'PS3','index'=>'KeyC'],
                        
            ['id'=>22,'value'=>'AA','index'=>'KeyC'],
            ['id'=>22,'value'=>'BB','index'=>'KeyD'],
            ['id'=>22,'value'=>'CC','index'=>'KeyE'],                                    
        ]);
    }

}