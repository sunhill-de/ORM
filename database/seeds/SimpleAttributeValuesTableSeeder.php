<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleAttributeValuesTableSeeder extends Seeder {
	
    public function run() {
        DB::table('attributevalues')->truncate();
        DB::table('attributevalues')->insert([
            [
                'id'=>1,
                'attribute_id'=>1,
                'object_id'=>1,
                'value'=>111,
                'textvalue'=>'',
            ],[
                'id'=>2,
                'attribute_id'=>2,
                'object_id'=>5,
                'value'=>121,
                'textvalue'=>'',
            ],[
                'id'=>3,
                'attribute_id'=>2,
                'object_id'=>6,
                'value'=>232,
                'textvalue'=>'',
            ],[
                'id'=>4,
                'attribute_id'=>3,
                'object_id'=>6,
                'value'=>666,
                'textvalue'=>'',
            ]
        ]);
    }
    
}