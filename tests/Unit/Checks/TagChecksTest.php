<?php
/**
 * @file tests/Unit/Checks/TagCheckTest.php
 * Tests the routine in TagChecks
 */
namespace Sunhill\ORM\Tests\Unit\Checks;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Checks\TagChecks;
use Sunhill\Basic\Facades\Checks;
use Sunhill\Basic\Checker\CheckException;
use Sunhill\Basic\Checker\Checker;

class TagChecksTest extends CheckTestCase
{
    
    /**
     * Tests: buildTagMatrix
     */
    public function testBuildTagMatrix()
    {
        $test = new TagChecks();
        $result = $this->callProtectedMethod($test, 'buildTagMatrix');
        
        $this->assertEquals('TagE',$result[8]->name);
    }
    
    protected function getMatrix()
    {
        $tag1 = new \StdClass();
        $tag1->id = 1;
        $tag1->name ='TagA';
        $tag1->parent_id = 0;
        $tag2 = new \StdClass();
        $tag2->id = 2;
        $tag2->name ='TagB';
        $tag2->parent_id = 1;
        $tag3 = new \StdClass();
        $tag3->id = 3;
        $tag3->name ='TagC';
        $tag3->parent_id = 2;
        $tag4 = new \StdClass();
        $tag4->id = 4;
        $tag4->name ='TagD';
        $tag4->parent_id = 0;        
        return [1=>$tag1,2=>$tag2,3=>$tag3,4=>$tag4];        
    }
    
    /**
     * Tests: getFullpath
     */
    public function testGetFullpath()
    {
        $test = new TagChecks();
        $matrix = $this->getMatrix();
        $this->assertEquals('TagA',$this->callProtectedMethod($test, 'getFullpath', [1, $matrix]));
        $this->assertEquals('TagA.TagB.TagC',$this->callProtectedMethod($test, 'getFullpath', [3, $matrix]));
    }
    
    /**
     * Tests: addPermutations
     */
    public function testAddPermutations()
    {
        $test = new TagChecks();
        $matrix = $this->getMatrix();
        $result = [];
        $this->callProtectedMethod($test, 'addPermutations', [&$result, $matrix, 3, 3]);
        usort($result,function($a,$b) { return ($a->path_name < $b->path_name)?-1:1;});
        $this->assertEquals(3,count($result));
        $this->assertEquals('TagA.TagB.TagC',$result[0]->path_name);
        $this->assertEquals(3,$result[0]->id);
    }
     
    /**
     * Tests: buildExpectedTagCache
     */
    public function testBuildExpectedTagCache()
    {
        $test = new TagChecks();
        $matrix = $this->getMatrix();
        $result = $this->callProtectedMethod($test, 'buildExpectedTagCache',[&$matrix]);
        usort($result,function($a,$b) { return ($a->path_name < $b->path_name)?-1:1;});
        
        $this->assertEquals(7,count($result));
        $this->assertEquals('TagD',$result[6]->path_name);
        $this->assertEquals(4,$result[6]->id);
    }
    
    /**
     * @dataProvider repairableProblemProvider
     * @param unknown $check
     * @param unknown $destroy_callback
     */
    public function testRepairableProblems($check, $destroy_callback)
    {
        $checker = new TagChecks();
        $this->assertEquals('passed',$this->runCheck($checker, $check, false));
        $destroy_callback();
        $this->assertEquals('failed',$this->runCheck($checker, $check, false));
        $this->assertEquals('repaired',$this->runCheck($checker, $check, true));
        $this->assertEquals('passed',$this->runCheck($checker, $check, false));
    }
    
    /**
     * Tests: see List
     */
    public static function repairableProblemProvider()
    {
        return [
            ['check_TagsWithNotExistingParents',function() {DB::table('tags')->where('id',7)->delete();} ],            
            ['check_TagCacheWithNotExistingTags',function() {DB::table('tags')->where('id',7)->delete();} ],            
            ['check_TagObjectAssignsTagsExist',function() {DB::table('tags')->where('id',4)->delete();} ],
            ['check_TagObjectAssignsObjectsExist',function() {DB::table('objects')->where('id',10)->delete();} ],
            ['check_ExpectedTagcacheEntries',function() {DB::table('tagcache')->where('path_name','TagD')->delete();} ],
            ['check_UnexpectedTagcacheEntries',function() {DB::table('tagcache')->insert(['path_name'=>'wrongtag','tag_id'=>4]);} ]
            ];
    }
    
    
}