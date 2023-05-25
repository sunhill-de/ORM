<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalcClassSeeder extends Seeder {
	
    public function run() {
        DB::table('calcclasses')->truncate();
        DB::table('calcclasses')->insert([
            ['id'=>31,'dummyint'=>123,'calcfield'=>'ABC','calcfield2'=>'ABC2'],
        ]);
    }

}