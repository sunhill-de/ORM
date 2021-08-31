<?php

namespace Tests\Unit\Managers;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Managers\tag_manager;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\ORMException;
use Illuminate\Support\Facades\DB;

define('NUMBER_OF_TAGS', 8);
define('NUMBER_OF_ORPHANED_TAGS', 6);
define('NUMBER_OF_ROOT_TAGS', 5);

class ManagerTagTest extends DBTestCase
{

    // ========================== Test count with different accessibilities  ==================================
    // total number of tags
    public function testCount() {
        $test = new tag_manager();
        $this->assertEquals(NUMBER_OF_TAGS,$test->get_count());
    }
    
    public function testCountViaApp() {
        $test = app('\Sunhill\ORM\Managers\tag_manager');
        $this->assertEquals(NUMBER_OF_TAGS,$test->get_count());
    }
    
    public function testCountViaFacade() {
        $this->assertEquals(NUMBER_OF_TAGS,Tags::get_count());        
    }
    
    // ========================== tests with orphaned tags ==============================    
    // Count orphaned tags
    /**
     * @group orphaned
     */
    public function testOrphanedCount() {
        $this->assertEquals(NUMBER_OF_ORPHANED_TAGS,Tags::get_orphaned_count());
    }
    
    // Find orphaned tags
    /**
     * @group orphaned
     */
    public function testAllOrphaned() {
        $this->assertEquals('TagC',Tags::get_all_orphaned()[0]->name);    
    }
    
    // Find orphaned tags
    /**
     * @group orphaned
     */
    public function testOrphaned() {
        $this->assertEquals('TagC',Tags::get_orphaned(0)->name);
    }

// ========================= tests with root tags ===================================    
    // total number of root tags
    /**
     * @group root
     */
    public function testRootCount() {
        $this->assertEquals(NUMBER_OF_ROOT_TAGS,Tags::get_root_count());
    }
                    
    // get 'index' root tags
    /**
     * @group root
     */
    public function testRoot() {
        
        $tag = Tags::get_root(1);
        $this->assertEquals('TagB',$tag->name);
    }

    // get all root tags
    /**
     * @group root
     */
    public function testAllRoot() {
        
        $this->assertEquals('TagB',Tags::get_all_root()[1]->name);    
    }

    // get 'index' tag
    /**
     * @group tag
     */
    public function testTag() {
        
        $this->assertEquals('TagB',Tags::get_tag(2)->name);
        $this->assertEquals('TagB',Tags::get_tag(2)->fullpath);
        $this->assertEquals(0,Tags::get_tag(2)->parent_id);
        $this->assertTrue(Tags::get_tag(2)->parent_name->empty());
    }

    /**
     * @group tag
     */
    public function testTagWithParent() {
        
        $this->assertEquals('TagE',Tags::get_tag(8)->name);
        $this->assertEquals('TagF.TagG.TagE',Tags::get_tag(8)->fullpath);
        $this->assertEquals(7,Tags::get_tag(8)->parent_id);
        $this->assertEquals('TagG',Tags::get_tag(8)->parent_name);
    }
    
    // get fullpath of 'index' tag
    public function testFullpathTag() {
        
        $this->assertEquals('TagF.TagG.TagE',Tags::get_tag_fullpath(8));
    }

    // get all Tags
    public function testAllTags() {
        
        $this->assertEquals('TagC',Tags::get_all_tags()[2]->name);        
    }
    
    // get all Tags with delta and limit
    public function testAllTagsWithDelta() {
        
        $this->assertEquals('TagC',Tags::get_all_tags(2,1)[0]->name);
    }
    
    // ========================== Test edit tags ==============================
    /**
     * @group change
     */
    public function testChangeTagName_TagChanged() {
        
        Tags::change_tag(3,['name'=>'NewTagC']);
        $this->assertEquals('NewTagC',Tags::get_tag(3)->name);
        $this->assertEquals('TagB.NewTagC',Tags::get_tag(3)->fullpath);
    }
    
    // check if the tag cache was updated after the name of a tag changed
    /**
     * @group change
     */
    public function testChangeTagName_CacheUpdated() {
        
        Tags::change_tag(3,['name'=>'NewTagC']);    
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[0]->name,'NewTagC');
    }
    
    // Change Parent of index tag
    /**
     * @group change
     */
    public function testChangeTagParent_TagChanged() {
        
        Tags::change_tag(3,['parent'=>'TagD']);   
        $this->assertEquals('TagD.TagC',Tags::get_tag(3)->fullpath);
    }

    // Check if tag cache was updated wheren parent of tag was changed
    /**
     * @group change
     */
    public function testChangeTagParent_CacheUpdated() {
        
        Tags::change_tag(3,['parent'=>'TagD']);
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[1]->name,'TagD.TagC');
        $this->assertEquals($result[0]->name,'TagC');
    }
    
    // Clear tags
    public function testClearTags_CacheEmpty() {
        Tags::clear_tags();
        $result = DB::table('tagcache')->get();
        $this->assertTrue($result->isEmpty());
    }
    
    public function testClearTags_ReferenceEmpty() {
        Tags::clear_tags();
        $result = DB::table('tagobjectassigns')->get();
        $this->assertTrue($result->isEmpty());
    }
    
    public function testClearTags_TagsEmpty() {
        Tags::clear_tags();
        $result = DB::table('tags')->get();
        $this->assertTrue($result->isEmpty());
    }
    
    // delete tag index
    /**
     * @group delete
     */
    public function testDeleteTag_TagDeleted() {
        
        Tags::delete_tag(3);
        $this->assertNull(Tags::get_tag(3));
    }
    
    // Check if tag cache was updated wheren tag was deleted
    /**
     * @group delete
     */
    public function testDeleteTag_CacheUpdated() {
        
        Tags::delete_tag(3);
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
    }
    
    // Check if tag object association was updated wheren tag was deleted
    /**
     * @group delete
     */
    public function testDeleteTag_AssociationsUpdated() {
        
        Tags::delete_tag(3);
        $result = DB::table('tagobjectassigns')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
    }
    
    /**
     * @dataProvider GetTagIDProvider
     * @group add
     */
    public function test_get_tag_id($parent,$expect) {
        $test = new tag_manager();
        if (is_callable($parent)) {
            $parent = $parent();
        }
        $tag = $this->callProtectedMethod($test,'get_tag_id',[$parent]);
        $this->assertEquals($expect,$tag->get_id());
    }
    
    public function GetTagIDProvider() {
        return [
            [null,0],
            [1,1],
            ['TagA',1],
            ['TagB.TagC',3],
            [function(){ return Tags::load_tag(3); },3]
        ];
    }
    
    /**
     * @group add
     */
    public function testExecuteAddTag_TagAdded() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'execute_add_tag',['Test','TagA']);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertEquals(1,$result->parent_id);                
    }
    
    /**
     * @group add
     */
    public function testExecuteAddTag_TagAddedNoParent() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'execute_add_tag',['Test',null]);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertEquals(0,$result->parent_id);                
    }
    
    /**
     * @group add
     */
    public function testExecuteAddTag_TagCacheAdded() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'execute_add_tag',['Test','TagA']);
        $result = DB::table('tagcache')->where('name','TagA.Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withString_no_parent() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'add_tag_by_string',['Test']);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withString_parent() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'add_tag_by_string',['TagA.Test']);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertEquals(1,$result->parent_id);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withString_missingparent() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'add_tag_by_string',['TagZ.Test']);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertTrue($result->parent_id>1);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withArray_no_parent() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'add_tag_by_string',[['name'=>'Test']]);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withArray_parent() {
        $test = new tag_manager();
        $this->callProtectedMethod($test,'add_tag_by_string',[['name'=>'Test','parent'=>'TagA']]);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertEquals(1,$result->parent_id);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withDescriptor_no_parent() {
        $test = new tag_manager();
        $descriptor = new descriptor();
        $descriptor->name = 'Test';
        $descriptor->parent = 'TagA';
        $this->callProtectedMethod($test,'add_tag_by_descriptor',[$descriptor]);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withDescriptor_parent() {
        $test = new tag_manager();
        $descriptor = new descriptor();
        $descriptor->name = 'Test';
        $descriptor->parent = 'TagA';
        $this->callProtectedMethod($test,'add_tag_by_descriptor',[$descriptor]);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertEquals(1,$result->parent_id);
    }
    
    /**
     * @group add
     * @dataProvider AddTagProvider
     */
    public function testAddTag($tag,$expect) {        
        if (is_callable($tag)) {
            $tag = $tag();
        }
        Tags::add_tag($tag);
        $result = DB::table('tags')->where('name',$expect)->get();
        $this->assertEquals($expect,$result[0]->name);
    }

    public function AddTagProvider() {
        return [
            ['Test','Test'],
            ['TagA.Test','Test'],
            [['name'=>'Test','Test']],
            [function() { $descriptor = new descriptor(); $descriptor->name = 'Test'; return $descriptor; },'Test'],
            [function() { $tag = new tag(); $tag->set_name('Test'); return $tag; },'Test'],
        ];
    }                                                                
                                                                
    // List all tags with a condition
    /**
     * @group list
     */
    public function testListConiditional() {
        
        $result = Tags::list_tags("name<'TagC'");
        $this->assertEquals('TagB',$result[1]->name);
    }

    // List all tags with delta and limit
    /**
     * @group list
     */
    public function testListConiditionalWithDelta() {
        
        $result = Tags::list_tags("name<'TagC'",1,1);
        $this->assertEquals('TagB',$result[0]->name);
    }
                                                                                                    
    // Search a tag with unique name
    /**
     * @group search
     */
    public function testSearchUnique() {
        
        $result = Tags::search_tag('TagA');
       $this->assertEquals(1,$result->id);
    }
    
    // Search a tag with multiple resuslts
    /**
     * @group search
     */
    public function testSearchMultiple() {
        
        $result = Tags::search_tag('TagE');
        $this->assertEquals(5,$result[0]->id);        
    }
    
    // Search a tag with no result
    /**
     * @group search
     */
    public function testSearchNoResult() {
        
        $result = Tags::search_tag('NonExistingTag');
        $this->assertNull($result);
    }
         
}
