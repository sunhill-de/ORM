<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestChildChildMapTableSeeder extends Seeder {
	
    public function run() {
        DB::table('testchildren_childmap')->truncate();
        DB::table('testchildren_childmap')->insert([
            ['id'=>17,'value'=>3,'index'=>'KeyA'],
            ['id'=>17,'value'=>4,'index'=>'KeyB'],
            ['id'=>17,'value'=>5,'index'=>'KeyC'],            
            ['id'=>18,'value'=>5,'index'=>'KeyD'],
            ['id'=>18,'value'=>6,'index'=>'KeyB'],
            ['id'=>18,'value'=>7,'index'=>'KeyE'],            
            ['id'=>20,'value'=>1,'index'=>'KeyA'],
            ['id'=>20,'value'=>3,'index'=>'KeyB'],            
            ['id'=>24,'value'=>1,'index'=>'KeyC'],
        ]);
    }

}