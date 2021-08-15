<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchStringObjectAssignsTableSeeder extends Seeder {
	
    public function run() {
        DB::table('stringobjectassigns')->truncate();
        DB::table('stringobjectassigns')->insert([
            ['container_id'=>7,'element_id'=>'testA','field'=>'Asarray','index'=>0],
            ['container_id'=>7,'element_id'=>'testB','field'=>'Asarray','index'=>1],
            ['container_id'=>8,'element_id'=>'testA','field'=>'Asarray','index'=>0],
            ['container_id'=>8,'element_id'=>'testC','field'=>'Asarray','index'=>1],
            ['container_id'=>13,'element_id'=>'testA','field'=>'Bsarray','index'=>0],
            ['container_id'=>13,'element_id'=>'testC','field'=>'Asarray','index'=>0],
        ]);
    }

}