<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExternalhooksTableSeeder extends Seeder {
	
    public function run() {
        $this->insert_into('externalhooks',['id','container_id','target_id','action','subaction','hook','payload'],
            [
                [1,1,2,'PROPERTY_UPDATED','dummyint','dummyint_updated',null],
                [2,2,1,'PROPERTY_UPDATED','dummyint','dummyint2_updated',null],
                [3,1,5,'PROPERTY_UPDATED','dummyint','dummyint3_updated',null]
            ]);
        DB::table('externalhooks')->insert([
            [
                'id'=>1,
                'container_id'=>1,
                'target_id'=>2,
                'action'=>'PROPERTY_UPDATED',
                'subactions'=>'dummyint',
                'hook'=>'dummyint_updated',
                'payload'=>null,
            ],[
                'id'=>2,
                'container_id'=>2,
                'target_id'=>1,
                'action'=>'PROPERTY_UPDATED',
                'subactions'=>'dummyint',
                'hook'=>'dummyint2_updated',
                'payload'=>null,
            ],[
                'id'=>3,
                'container_id'=>1,
                'target_id'=>5,
                'action'=>'PROPERTY_UPDATED',
                'subactions'=>'dummyint',
                'hook'=>'dummyint3_updated',
                'payload'=>null,
            ]            
        ]);
    }
    
}