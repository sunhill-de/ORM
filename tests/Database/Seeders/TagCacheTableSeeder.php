<?php
namespace Sunhill\ORM\Tests\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagCacheTableSeeder extends Seeder {
	
	public function run() {
	    DB::table('tagcache')->truncate();
	    DB::table('tagcache')->insert([
	        ['id'=>1,'path_name'=>'TagA','tag_id'=>1,'is_fullpath'=>true],
	        ['id'=>2,'path_name'=>'TagB','tag_id'=>2,'is_fullpath'=>true],
	        ['id'=>3,'path_name'=>'TagC','tag_id'=>3,'is_fullpath'=>false],
	        ['id'=>4,'path_name'=>'TagB.TagC','tag_id'=>3,'is_fullpath'=>true],
	        ['id'=>5,'path_name'=>'TagD','tag_id'=>4,'is_fullpath'=>true],
	        ['id'=>6,'path_name'=>'TagE','tag_id'=>5,'is_fullpath'=>true],
	        ['id'=>7,'path_name'=>'TagF','tag_id'=>6,'is_fullpath'=>true],		
	        ['id'=>8,'path_name'=>'TagG','tag_id'=>7,'is_fullpath'=>false],
	        ['id'=>9,'path_name'=>'TagF.TagG','tag_id'=>7,'is_fullpath'=>true],
	        ['id'=>10,'path_name'=>'TagE','tag_id'=>8,'is_fullpath'=>false],
	        ['id'=>11,'path_name'=>'TagG.TagE','tag_id'=>8,'is_fullpath'=>false],
	        ['id'=>12,'path_name'=>'TagF.TagG.TagE','tag_id'=>8,'is_fullpath'=>true],
	        ['id'=>13,'path_name'=>'TagZ','tag_id'=>9,'is_fullpath'=>true],
	    ]);
	}
}