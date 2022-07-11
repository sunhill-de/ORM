<?php
namespace Sunhill\ORM\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchtestBTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('searchtestB')->truncate();
	    DB::table('searchtestB')->insert([
	        ['id'=>10,'Bint'=>111,'Bchar'=>'ABC'],
	        ['id'=>11,'Bint'=>601,'Bchar'=>'BBB'],
	        ['id'=>12,'Bint'=>602,'Bchar'=>'CCC'],
	        ['id'=>13,'Bint'=>602,'Bchar'=>'DDC'],
	        ['id'=>14,'Bint'=>603,'Bchar'=>'ADD'],
	        ['id'=>15,'Bint'=>603,'Bchar'=>'GGG']
		]);
	}
}