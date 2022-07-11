<?php
namespace Sunhill\ORM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleAttributeValuesTableSeeder extends Seeder {
	
    public function run() {
        DB::table('attributevalues')->truncate();
        DB::table('attributevalues')->insert([
            [
                 'attribute_id'=>1,
                'object_id'=>1,
                'value'=>111,
                'textvalue'=>'',
            ],[
                 'attribute_id'=>2,
                'object_id'=>5,
                'value'=>121,
                'textvalue'=>'',
            ],[
                'attribute_id'=>2,
                'object_id'=>6,
                'value'=>232,
                'textvalue'=>'',
            ],[
                'attribute_id'=>3,
                'object_id'=>6,
                'value'=>666,
                'textvalue'=>'',
            ]
        ]);
    }
    
}