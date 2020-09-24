<?php

namespace Tests\Unit;

use Manager\Managers\tag_manager;
use PHPUnit\Framework\MockObject\Matcher\Parameters;
use Illuminate\Support\Facades\DB;

class ManagerTagTest extends \Manager\Tests\FilesystemStdTestCase
{

// ========================== tests with orphaned tags ==============================    
    // Count orphaned tags
    /**
     * @group orphaned
     */
    public function testOrphanedCount() {
        $this->setup_scenario();
        $this->assertEquals(NUMBER_OF_ORPHANED_TAGS,tag_manager::get_orphaned_count());
    }
    
    // Find orphaned tags
    /**
     * @group orphaned
     */
    public function testAllOrphaned() {
        $this->setup_scenario();
        $this->assertEquals('TagD',tag_manager::get_all_orphaned()[0]->name);    
    }
    
    // Find orphaned tags
    /**
     * @group orphaned
     */
    public function testOrphaned() {
        $this->setup_scenario();
        $this->assertEquals('TagD',tag_manager::get_orphaned(0)->name);
    }

// ========================= tests with root tags ===================================    
    // total number of root tags
    /**
     * @group root
     */
    public function testRootCount() {
        $this->setup_scenario();
        $this->assertEquals(NUMBER_OF_ROOT_TAGS,tag_manager::get_root_count());
    }
                    
    // get 'index' root tags
    /**
     * @group root
     */
    public function testRoot() {
        $this->setup_scenario();
        $tag = tag_manager::get_root(1);
        $this->assertEquals('TagB',$tag->name);
    }

    // get all root tags
    /**
     * @group root
     */
    public function testAllRoot() {
        $this->setup_scenario();
        $this->assertEquals('TagB',tag_manager::get_all_root()[1]->name);    
    }

// ========================== tests with all tags ==================================    
    // total number of tags
    public function testCount() {
        $this->setup_scenario();
        $this->assertEquals(NUMBER_OF_TAGS,tag_manager::get_count());
    }
    
    // get 'index' tag
    /**
     * @group tag
     */
    public function testTag() {
        $this->setup_scenario();
        $this->assertEquals('TagB',tag_manager::get_tag(2)->name);
        $this->assertEquals('TagB',tag_manager::get_tag(2)->fullpath);
        $this->assertEquals(0,tag_manager::get_tag(2)->parent_id);
        $this->assertTrue(tag_manager::get_tag(2)->parent_name->empty());
    }

    /**
     * @group tag
     */
    public function testTagWithParent() {
        $this->setup_scenario();
        $this->assertEquals('TagF',tag_manager::get_tag(6)->name);
        $this->assertEquals('TagD.TagE.TagF',tag_manager::get_tag(6)->fullpath);
        $this->assertEquals(5,tag_manager::get_tag(6)->parent_id);
        $this->assertEquals('TagE',tag_manager::get_tag(6)->parent_name);
    }
    
    // get fullpath of 'index' tag
    public function testFullpathTag() {
        $this->setup_scenario();
        $this->assertEquals('TagD.TagE.TagF',tag_manager::get_tag_fullpath(6));
    }

    // get all Tags
    public function testAllTags() {
        $this->setup_scenario();
        $this->assertEquals('TagC',tag_manager::get_all_tags()[2]->name);        
    }
    
    // get all Tags with delta and limit
    public function testAllTagsWithDelta() {
        $this->setup_scenario();
        $this->assertEquals('TagC',tag_manager::get_all_tags(2,1)[0]->name);
    }
    
    // ========================== Test edit tags ==============================
    /**
     * @group change
     */
    public function testChangeTagName_TagChanged() {
        $this->setup_scenario();
        tag_manager::change_tag(3,['name'=>'NewTagC']);
        $this->assertEquals('NewTagC',tag_manager::get_tag(3)->name);
        $this->assertEquals('TagB.NewTagC',tag_manager::get_tag(3)->fullpath);
    }
    
    // check if the file links were updated after the name of a tag changed
    /**
     * @group change
     */
    public function testChangeTagName_LinksUpdated() {
        $this->setup_scenario();
        tag_manager::change_tag(3,['name'=>'NewTagC']);
        $tmpdir = $this->get_temp_dir();
        $this->assertEquals('qqq.qqqB',file_get_contents($tmpdir.'/media/tags/TagB/NewTagC/B.qqq'));
        $this->assertFalse(file_exists($tmpdir.'/media/tags/TagB/TagC/B.qqq'));
    }
    
    // check if the tag cache was updated after the name of a tag changed
    /**
     * @group change
     */
    public function testChangeTagName_CacheUpdated() {
        $this->setup_scenario();
        tag_manager::change_tag(3,['name'=>'NewTagC']);    
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[0]->name,'NewTagC');
    }
    
    // Change Parent of index tag
    /**
     * @group change
     */
    public function testChangeTagParent_TagChanged() {
        $this->setup_scenario();
        tag_manager::change_tag(3,['parent'=>'TagD']);   
        $this->assertEquals('TagD.TagC',tag_manager::get_tag(3)->fullpath);
    }

    // Check if file links where updated when parent of tag was changed
    /**
     * @group change
     */
    public function testChangeTagParent_LinksUpdated() {
        $this->setup_scenario();
        tag_manager::change_tag(3,['parent'=>'TagD']);
        $tmpdir = $this->get_temp_dir();
        $this->assertEquals('qqq.qqqB',file_get_contents($tmpdir.'/media/tags/TagD/TagC/B.qqq'));
        $this->assertFalse(file_exists($tmpdir.'/media/tags/TagB/TagC/B.qqq'));
    }
    
    // Check if tag cache was updated wheren parent of tag was changed
    /**
     * @group change
     */
    public function testChangeTagParent_CacheUpdated() {
        $this->setup_scenario();
        tag_manager::change_tag(3,['parent'=>'TagD']);
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[1]->name,'TagD.TagC');
        $this->assertEquals($result[0]->name,'TagC');
    }
    
    // delete tag index
    /**
     * @group delete
     */
    public function testDeleteTag_TagDeleted() {
        $this->setup_scenario();
        tag_manager::delete_tag(3);
        $this->assertNull(tag_manager::get_tag(3));
    }
    
    // Check if file links where removed when tag was deleted
    /**
     * @group delete
     */
    public function testDeleteTag_LinksUpdated() {
        $this->setup_scenario();
        tag_manager::delete_tag(3);
        $tmpdir = $this->get_temp_dir();
        $this->assertFalse(file_exists($tmpdir.'/media/tags/TagB/TagC/B.qqq'));
    }
        
    // Check if tag cache was updated wheren tag was deleted
    /**
     * @group delete
     */
    public function testDeleteTag_CacheUpdated() {
        $this->setup_scenario();
        tag_manager::delete_tag(3);
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
    }
    
    // Check if tag object association was updated wheren tag was deleted
    /**
     * @group delete
     */
    public function testDeleteTag_AssociationsUpdated() {
        $this->setup_scenario();
        tag_manager::delete_tag(3);
        $result = DB::table('tagobjectassigns')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
    }
        
    // Add a tag (tag table updated?)
    /**
     * @group add
     */
    public function testAddTag_TagAdded() {
        $this->setup_scenario();
        tag_manager::add_tag(['name'=>'TagZ','parent'=>'TagA']);
        $result = DB::table('tags')->where('name','TagZ')->get();
        $this->assertEquals('TagZ',$result[0]->name);
    }

    // Add a tag (tag table updated?)
    /**
     * @group add
     */
    public function testAddTag_TagAdded_noparent() {
        $this->setup_scenario();
        tag_manager::add_tag(['name'=>'TagZ']);
        $result = DB::table('tags')->where('name','TagZ')->get();
        $this->assertEquals(0,$result[0]->parent_id);
    }

    // Add a tag (tag cache updated?)
    /**
     * @group add
     */
    public function testAddTag_CacheUpdated() {
        $this->setup_scenario();
        tag_manager::add_tag(['name'=>'TagZ','parent'=>'TagA']);
        $result = DB::table('tagcache')->where('name','TagA.TagZ')->get();
        $this->assertTrue($result->count()>0);
    }
                                                                                        
    // List all tags with a condition
    /**
     * @group list
     */
    public function testListConiditional() {
        $this->setup_scenario();
        $result = tag_manager::list_tags("name<'TagC'");
        $this->assertEquals('TagB',$result[1]->name);
    }

    // List all tags with delta and limit
    /**
     * @group list
     */
    public function testListConiditionalWithDelta() {
        $this->setup_scenario();
        $result = tag_manager::list_tags("name<'TagC'",1,1);
        $this->assertEquals('TagB',$result[0]->name);
    }
                                                                                                    
    // Search a tag with unique name
    /**
     * @group search
     */
    public function testSearchUnique() {
        $this->setup_scenario();
        $result = tag_manager::search_tag('TagA');
       $this->assertEquals(1,$result->id);
    }
    
    // Search a tag with multiple resuslts
    /**
     * @group search
     */
    public function testSearchMultiple() {
        $this->setup_scenario();
        $result = tag_manager::search_tag('TagH');
        $this->assertEquals(7,$result[0]->id);        
    }
    
    // Search a tag with no result
    /**
     * @group search
     */
    public function testSearchNoResult() {
        $this->setup_scenario();
        $result = tag_manager::search_tag('NonExistingTag');
        $this->assertNull($result);
    }
         
    /**
     * Search all files that have this tag
     * @group files
     */
    public function testSearchFilesToTag() {
        $files = tag_manager::search_files('TagA');
        $this->assertEquals('B',$files[0]->name);
    }
    
    /**
     * @group dirdescriptor
     */
    public function testGetTagDirDescriptor() {
        $descriptor = tag_manager::get_dir_descriptor('TagD.TagE.TagF');
        $this->assertEquals('TagD/TagE/TagF/',$descriptor->get_dir());
        $this->assertEquals(5,$descriptor[1]->tag_id);
    }
}
