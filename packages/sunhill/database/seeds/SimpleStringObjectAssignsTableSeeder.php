<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleStringObjectAssignsTableSeeder extends Seeder {
	
    public function run() {
        DB::table('stringobjectassigns')->truncate();
        DB::table('stringobjectassigns')->insert([
            [
                'container_id'=>5,
                'element_id'=>'ObjectString0',
                'field'=>'parentsarray',
                'index'=>0
            ],[
                'container_id'=>5,
                'element_id'=>'ObjectString1',
                'field'=>'parentsarray',
                'index'=>1
            ],[
                'container_id'=>6,
                'element_id'=>'Parent0',
                'field'=>'parentsarray',
                'index'=>0
            ],[
                'container_id'=>6,
                'element_id'=>'Parent1',
                'field'=>'parentsarray',
                'index'=>1
            ],[
                'container_id'=>6,
                'element_id'=>'Child0',
                'field'=>'childsarray',
                'index'=>0
            ],[
                'container_id'=>6,
                'element_id'=>'Child1',
                'field'=>'childsarray',
                'index'=>1
            ],[
                'container_id'=>6,
                'element_id'=>'Child2',
                'field'=>'childsarray',
                'index'=>2
            ]
        ]);
    }

}