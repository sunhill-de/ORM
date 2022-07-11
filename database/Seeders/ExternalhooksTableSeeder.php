<?php
namespace Sunhill\ORM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExternalhooksTableSeeder extends Seeder {
	
    public function run() {
        DB::table('externalhooks')->truncate();
        DB::table('externalhooks')->insert([
            [
                'id'=>1,
                'container_id'=>1,
                'target_id'=>2,
                'action'=>'PROPERTY_UPDATED',
                'subaction'=>'dummyint',
                'hook'=>'dummyint_updated',
                'payload'=>null,
            ],[
                'id'=>2,
                'container_id'=>2,
                'target_id'=>1,
                'action'=>'PROPERTY_UPDATED',
                'subaction'=>'dummyint',
                'hook'=>'dummyint2_updated',
                'payload'=>null,
            ],[
                'id'=>3,
                'container_id'=>1,
                'target_id'=>5,
                'action'=>'PROPERTY_UPDATED',
                'subaction'=>'dummyint',
                'hook'=>'dummyint3_updated',
                'payload'=>null,
            ]            
        ]);
    }
    
}