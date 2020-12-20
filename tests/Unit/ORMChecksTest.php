<?php
namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Checks\orm_checks;
use Illuminate\Support\Facades\DB;

class ORMChecksTest extends TestCase
{
    public function testTagswithnotexistingparents_pass() {
        DB::statement('truncate tags');
        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>1,'options'=>0],            
        ]);
        $test = new orm_checks();
        $result = $test->check_tagswithnotexistingparents();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testTagswithnotexistingparents_fail() {
        DB::statement('truncate tags');
        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>100,'options'=>0],
        ]);
        $test = new orm_checks();
        $result = $test->check_tagswithnotexistingparents();
        $this->assertEquals('FAILED',$result->result);        
    }

    public function testTagcachewithnotexistingtags_pass() {
        DB::statement('truncate tags');
        DB::statement('truncate tagcache');
        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>1,'options'=>0],
        ]);
        DB::table('tagcache')->insert([
            ['name'=>'TagA','tag_id'=>1], 
            ['name'=>'TagB','tag_id'=>2],
            ['name'=>'TagA.TagB','tag_id'=>2],
            ['name'=>'TagC','tag_id'=>3],
            ['name'=>'TagA.TagC','tag_id'=>3],
        ]);
        $test = new orm_checks();
        $result = $test->check_tagcachewithnotexistingtags();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testTagcachewithnotexistingtags_fail() {
        DB::statement('truncate tags');
        DB::statement('truncate tagcache');
        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>1,'options'=>0],
        ]);
        DB::table('tagcache')->insert([
            ['name'=>'TagA','tag_id'=>1],
            ['name'=>'TagB','tag_id'=>2],
            ['name'=>'TagA.TagB','tag_id'=>2],
            ['name'=>'TagC','tag_id'=>100],
            ['name'=>'TagA.TagC','tag_id'=>100],
        ]);
        $test = new orm_checks();
        $result = $test->check_tagcachewithnotexistingtags();
        $this->assertEquals('FAILED',$result->result);
    }
    
}
