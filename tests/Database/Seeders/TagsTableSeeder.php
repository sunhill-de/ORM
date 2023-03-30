<?php

namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder {
	
	public function run() {
		DB::table('tags')->truncate();
	    DB::table('tags')->insert([
		    ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
		    ['id'=>2,'name'=>'TagB','parent_id'=>0,'options'=>0],
		    ['id'=>3,'name'=>'TagC','parent_id'=>2,'options'=>0],
		    ['id'=>4,'name'=>'TagD','parent_id'=>0,'options'=>0],
		    ['id'=>5,'name'=>'TagE','parent_id'=>0,'options'=>0],
		    ['id'=>6,'name'=>'TagF','parent_id'=>0,'options'=>0],		    
		    ['id'=>7,'name'=>'TagG','parent_id'=>6,'options'=>0],
		    ['id'=>8,'name'=>'TagE','parent_id'=>7,'options'=>0],
	        ['id'=>9,'name'=>'TagZ','parent_id'=>0,'options'=>0],
	    ]);
	}
}