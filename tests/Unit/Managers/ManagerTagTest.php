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
        
    // Add a tag (tag table updated?)
    /**
     * @group add
     */
    public function testAddTag_TagAdded() {
        
        Tags::add_tag(['name'=>'TagZ','parent'=>'TagA']);
        $result = DB::table('tags')->where('name','TagZ')->get();
        $this->assertEquals('TagZ',$result[0]->name);
    }

    // Add a tag (tag table updated?)
    /**
     * @group add
     */
    public function testAddTag_TagAdded_noparent() {
        
        Tags::add_tag(['name'=>'TagZ']);
        $result = DB::table('tags')->where('name','TagZ')->get();
        $this->assertEquals(0,$result[0]->parent_id);
    }

    // Add a tag (tag cache updated?)
    /**
     * @group add
     */
    public function testAddTag_CacheUpdated() {
        
        Tags::add_tag(['name'=>'TagZ','parent'=>'TagA']);
        $result = DB::table('tagcache')->where('name','TagA.TagZ')->get();
        $this->assertTrue($result->count()>0);
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
