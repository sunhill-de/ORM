<?php
namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Checks\orm_checks;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;

use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Tests\Objects\ts_testchild;
use Sunhill\ORM\Tests\Objects\ts_testparent;
use Sunhill\ORM\Tests\Objects\ts_passthru;
use Sunhill\ORM\Tests\Objects\ts_secondlevelchild;
use Sunhill\ORM\Tests\Objects\ts_thirdlevelchild;
use Sunhill\ORM\Tests\Objects\ts_referenceonly;
use Sunhill\ORM\Tests\Objects\ts_objectunit;

class ORMChecksTest extends TestCase
{
    public function setUp() : void {
        parent::setUp();
        Classes::flushClasses();
        Classes::registerClass(ts_dummy::class);
        Classes::registerClass(ts_testparent::class);
        Classes::registerClass(ts_testchild::class);
        Classes::registerClass(ts_referenceonly::class);
        Classes::registerClass(ts_passthru::class);
        Classes::registerClass(ts_secondlevelchild::class);
        Classes::registerClass(ts_thirdlevelchild::class);
        Classes::registerClass(ts_objectunit::class);
    }
    
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

    public function testTagCacheConsitency_pass() {
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
        $result = $test->check_tagcacheconsistency();
        $this->assertEquals('OK',$result->result);        
    }
    
    public function testTagCacheConsitency_fail1() {
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
            ['name'=>'TagE.TagC','tag_id'=>3],
        ]);
        $test = new orm_checks();
        $result = $test->check_tagcacheconsistency();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testTagCacheConsitency_fail2() {
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
        ]);
        $test = new orm_checks();
        $result = $test->check_tagcacheconsistency();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testTagObjectAssignsTagExists_pass() {
        DB::statement('truncate tags');
        DB::statement('truncate objects');
        DB::statement('truncate tagobjectassigns');

        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>1,'options'=>0],
        ]);
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('tagobjectassigns')->insert([
            ['container_id'=>1,'tag_id'=>1],
            ['container_id'=>2,'tag_id'=>2],
        ]);        
        $test = new orm_checks();
        $result = $test->check_tagobjectassignstagsexist();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testTagObjectAssignsTagExists_fail() {
        DB::statement('truncate tags');
        DB::statement('truncate objects');
        DB::statement('truncate tagobjectassigns');
        
        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>1,'options'=>0],
        ]);
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('tagobjectassigns')->insert([
            ['container_id'=>1,'tag_id'=>1],
            ['container_id'=>2,'tag_id'=>100],
        ]);
        $test = new orm_checks();
        $result = $test->check_tagobjectassignstagsexist();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testTagObjectAssignsObjectExists_pass() {
        DB::statement('truncate tags');
        DB::statement('truncate objects');
        DB::statement('truncate tagobjectassigns');
        
        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>1,'options'=>0],
        ]);
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('tagobjectassigns')->insert([
            ['container_id'=>1,'tag_id'=>1],
            ['container_id'=>2,'tag_id'=>2],
        ]);
        $test = new orm_checks();
        $result = $test->check_tagobjectassignsobjectsexist();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testTagObjectAssignsObjectExists_fail() {
        DB::statement('truncate tags');
        DB::statement('truncate objects');
        DB::statement('truncate tagobjectassigns');
        
        DB::table('tags')->insert([
            ['id'=>1,'name'=>'TagA','parent_id'=>0,'options'=>0],
            ['id'=>2,'name'=>'TagB','parent_id'=>1,'options'=>0],
            ['id'=>3,'name'=>'TagC','parent_id'=>1,'options'=>0],
        ]);
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('tagobjectassigns')->insert([
            ['container_id'=>1,'tag_id'=>1],
            ['container_id'=>100,'tag_id'=>2],
        ]);
        
        $test = new orm_checks();
        $result = $test->check_tagobjectassignsobjectsexist();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testObjectObjectAssignsContainerExists_pass() {
        DB::statement('truncate objects');
        DB::statement('truncate objectobjectassigns');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('objectobjectassigns')->insert([
            ['container_id'=>1,'element_id'=>1,'field'=>'test','index'=>0],
            ['container_id'=>2,'element_id'=>2,'field'=>'test','index'=>0],
        ]);
        $test = new orm_checks();
        $result = $test->check_objectobjectassignscontainerexist();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testObjectObjectAssignsContainerExists_fail() {
        DB::statement('truncate objects');
        DB::statement('truncate objectobjectassigns');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('objectobjectassigns')->insert([
            ['container_id'=>1,'element_id'=>1,'field'=>'test','index'=>0],
            ['container_id'=>100,'element_id'=>2,'field'=>'test','index'=>0],
        ]);
        $test = new orm_checks();
        $result = $test->check_objectobjectassignscontainerexist();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testObjectObjectAssignsElementExists_pass() {
        DB::statement('truncate objects');
        DB::statement('truncate objectobjectassigns');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('objectobjectassigns')->insert([
            ['container_id'=>1,'element_id'=>1,'field'=>'test','index'=>0],
            ['container_id'=>2,'element_id'=>2,'field'=>'test','index'=>0],
        ]);
        $test = new orm_checks();
        $result = $test->check_objectobjectassignselementexist();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testObjectObjectAssignsElementExists_fail() {
        DB::statement('truncate objects');
        DB::statement('truncate objectobjectassigns');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('objectobjectassigns')->insert([
            ['container_id'=>1,'element_id'=>1,'field'=>'test','index'=>0],
            ['container_id'=>2,'element_id'=>100,'field'=>'test','index'=>0],
        ]);
        $test = new orm_checks();
        $result = $test->check_objectobjectassignselementexist();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testStringObjectAssignsElementExists_pass() {
        DB::statement('truncate objects');
        DB::statement('truncate stringobjectassigns');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('stringobjectassigns')->insert([
            ['container_id'=>1,'element_id'=>'test1','field'=>'test','index'=>0],
            ['container_id'=>2,'element_id'=>'test2','field'=>'test','index'=>0],
        ]);
        
        $test = new orm_checks();
        $result = $test->check_stringobjectassignscontainerexist();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testStringObjectAssignsElementExists_fail() {
        DB::statement('truncate objects');
        DB::statement('truncate stringobjectassigns');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'test'],
            ['id'=>2,'classname'=>'test'],
            ['id'=>3,'classname'=>'test'],
        ]);
        DB::table('stringobjectassigns')->insert([
            ['container_id'=>1,'element_id'=>'test1','field'=>'test','index'=>0],
            ['container_id'=>100,'element_id'=>'test2','field'=>'test','index'=>0],
        ]);
        
        $test = new orm_checks();
        $result = $test->check_stringobjectassignscontainerexist();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testObjectExistance_pass() {

        DB::statement('truncate objects');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'dummy'],
            ['id'=>2,'classname'=>'testparent'],
            ['id'=>3,'classname'=>'testchild'],
        ]);
        
        $test = new orm_checks();
        $result = $test->check_objectexistance();
        $this->assertEquals('OK',$result->result);
    }
    
    public function testObjectExistance_fail() {
        
        DB::statement('truncate objects');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'dummy'],
            ['id'=>2,'classname'=>'testparent'],
            ['id'=>3,'classname'=>'notexisting'],
        ]);
        
        $test = new orm_checks();
        $result = $test->check_objectexistance();
        $this->assertEquals('FAILED',$result->result);
    }
    
    public function testClassTableGaps_pass() {
        DB::statement('truncate objects');
        DB::statement('truncate dummies');
        DB::statement('truncate testparents');
        DB::statement('truncate testchildren');
        DB::statement('truncate passthrus');
        DB::statement('truncate secondlevelchildren');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'dummy'],
            ['id'=>2,'classname'=>'testparent'],
            ['id'=>3,'classname'=>'testchild'],
            ['id'=>4,'classname'=>'secondlevelchild'],
         ]);   
        DB::table('dummies')->insert([
            ['id'=>1,'dummyint'=>1],
        ]);
        
        DB::table('testparents')->insert([
            ['id'=>2,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A'],
            ['id'=>3,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A'],
            ['id'=>4,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A']
        ]);
        DB::table('testchildren')->insert([
            ['id'=>3,'childint'=>1,'childchar'=>'A','childfloat'=>1.1,'childdate'=>'2020-12-20','childtime'=>'12:00:00','childdatetime'=>'2020-12-20 12:00:00','childenum'=>'testA','childtext'=>'A'],
        ]);
        DB::table('passthrus')->insert([['id'=>4]]);
        DB::table('secondlevelchildren')->insert([['id'=>4,'childint'=>1]]);

        $test = new orm_checks();
        $result = $test->check_classtablegaps();
        $this->assertEquals('OK',$result->result);
        
    }
    
    public function testClassTableGaps_fail1() {
        DB::statement('truncate objects');
        DB::statement('truncate dummies');
        DB::statement('truncate testparents');
        DB::statement('truncate testchildren');
        DB::statement('truncate passthrus');
        DB::statement('truncate secondlevelchildren');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'dummy'],
            ['id'=>2,'classname'=>'testparent'],
            ['id'=>3,'classname'=>'testchild'],
            ['id'=>4,'classname'=>'secondlevelchild'],
        ]);
        DB::table('dummies')->insert([
            ['id'=>1,'dummyint'=>1],
        ]);
        
        DB::table('testparents')->insert([
            ['id'=>2,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A'],
            ['id'=>3,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A'],
            ['id'=>4,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A']
        ]);
        DB::table('testchildren')->insert([
            ['id'=>3,'childint'=>1,'childchar'=>'A','childfloat'=>1.1,'childdate'=>'2020-12-20','childtime'=>'12:00:00','childdatetime'=>'2020-12-20 12:00:00','childenum'=>'testA','childtext'=>'A'],
        ]);
        DB::table('secondlevelchildren')->insert([['id'=>4,'childint'=>1]]);
        
        $test = new orm_checks();
        $result = $test->check_classtablegaps();
        $this->assertEquals('FAILED',$result->result);        
    }
    
    public function testClassTableGaps_fail2() {
        DB::statement('truncate dummies');
        DB::statement('truncate testparents');
        DB::statement('truncate testchildren');
        DB::statement('truncate passthrus');
        DB::statement('truncate secondlevelchildren');
        DB::statement('truncate objects');
        
        DB::table('objects')->insert([
            ['id'=>1,'classname'=>'dummy'],
            ['id'=>2,'classname'=>'testparent'],
            ['id'=>4,'classname'=>'secondlevelchild'],
        ]);
        DB::table('dummies')->insert([
            ['id'=>1,'dummyint'=>1],
        ]);
        
        DB::table('testparents')->insert([
            ['id'=>2,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A'],
            ['id'=>3,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A'],
            ['id'=>4,'parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parentdate'=>'2020-12-20','parenttime'=>'12:00:00','parentdatetime'=>'2020-12-20 12:00:00','parentenum'=>'testA','parenttext'=>'A']
        ]);
        DB::table('testchildren')->insert([
            ['id'=>3,'childint'=>1,'childchar'=>'A','childfloat'=>1.1,'childdate'=>'2020-12-20','childtime'=>'12:00:00','childdatetime'=>'2020-12-20 12:00:00','childenum'=>'testA','childtext'=>'A'],
        ]);
        DB::table('passthrus')->insert([['id'=>4]]);
        DB::table('secondlevelchildren')->insert([['id'=>4,'childint'=>1]]);
        
        $test = new orm_checks();
        $result = $test->check_classtablegaps();
        $this->assertEquals('FAILED',$result->result);
    }
    
}
