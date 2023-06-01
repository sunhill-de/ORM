<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Managers\TagManager;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Objects\Tag;
use Illuminate\Support\Facades\DB;
use Sunhill\Basic\Utils\Descriptor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagManagerTest extends DatabaseTestCase
{
    // ========================== Test edit tags ==============================
    /**
     * @group change
     */
    public function testChangeTagName_TagChanged() {
        
        Tags::changeTag(3,['name'=>'NewTagC']);
        $this->assertEquals('NewTagC',Tags::getTag(3)->name);
        $this->assertEquals('TagB.NewTagC',Tags::getTag(3)->fullpath);
    }
    
    // check if the tag cache was updated after the name of a tag changed
    /**
     * @group change
     */
    public function testChangeTagName_CacheUpdated() {
        
        Tags::changeTag(3,['name'=>'NewTagC']);    
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[0]->path_name,'NewTagC');
    }
    
    // Change Parent of index tag
    /**
     * @group change
     */
    public function testChangeTagParent_TagChanged() {
        
        Tags::changeTag(3,['parent'=>'TagD']);   
        $this->assertEquals('TagD.TagC',Tags::getTag(3)->fullpath);
    }

    // Check if tag cache was updated wheren parent of tag was changed
    /**
     * @group change
     */
    public function testChangeTagParent_CacheUpdated() {
        
        Tags::changeTag(3,['parent'=>'TagD']);
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertEquals($result[1]->path_name,'TagD.TagC');
        $this->assertEquals($result[0]->path_name,'TagC');
    }
    
    // Clear tags
    public function testClearTags_CacheEmpty() {
        Tags::clearTags();
        $result = DB::table('tagcache')->get();
        $this->assertTrue($result->isEmpty());
    }
    
    public function testClearTags_ReferenceEmpty() {
        Tags::clearTags();
        $result = DB::table('tagobjectassigns')->get();
        $this->assertTrue($result->isEmpty());
    }
    
    public function testClearTags_TagsEmpty() {
        Tags::clearTags();
        $result = DB::table('tags')->get();
        $this->assertTrue($result->isEmpty());
    }
    
    // delete tag index
    /**
     * @group delete
     */
    public function testDeleteTag_TagDeleted() {
        
        Tags::deleteTag(3);
        $this->assertNull(Tags::getTag(3));
    }
    
    // Check if tag cache was updated wheren tag was deleted
    /**
     * @group delete
     */
    public function testDeleteTag_CacheUpdated() {
        
        Tags::deleteTag(3);
        $result = DB::table('tagcache')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
    }
    
    // Check if tag object association was updated wheren tag was deleted
    /**
     * @group delete
     */
    public function testDeleteTag_AssociationsUpdated() {
        
        Tags::deleteTag(3);
        $result = DB::table('tagobjectassigns')->where('tag_id',3)->get();
        $this->assertTrue($result->isEmpty());
    }
    
    /**
     * @dataProvider GetTagIDProvider
     * @group add
     */
    public function test_get_tag_id($parent,$expect) {
        $test = new TagManager();
        if (is_callable($parent)) {
            $parent = $parent();
        }
        $tag = $this->callProtectedMethod($test,'getTagID',[$parent]);
        $this->assertEquals($expect,$tag);
    }
    
    public function GetTagIDProvider() {
        return [
            [null,0],
            [1,1],
            ['TagA',1],
            ['TagB.TagC',3],
            [function(){ return Tags::loadTag(3); },3]
        ];
    }
    
    /**
     * @group add
     */
    public function testExecuteAddTag_TagAdded() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'executeAddTag',['Test','TagA']);
        $result = DB::table('tags')->where('name','Test')->first();
        $this->assertEquals(1,$result->parent_id);                
    }
    
    /**
     * @group add
     */
    public function testExecuteAddTag_TagAddedNoParent() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'executeAddTag',['Test',null]);
        $result = DB::table('tags')->where('name','Test')->first();
        $this->assertEquals(0,$result->parent_id);                
    }
    
    /**
     * @group add
     */
    public function testExecuteAddTag_TagCacheAdded() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'executeAddTag',['Test','TagA']);
        $result = DB::table('tagcache')->where('path_name','TagA.Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withString_no_parent() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'addTagByString',['Test']);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withString_parent() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'addTagByString',['TagA.Test']);
        $result = DB::table('tags')->where('name','Test')->first();
        $this->assertEquals(1,$result->parent_id);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withString_missingparent() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'addTagByString',['TagZ.Test']);
        $result = DB::table('tags')->where('name','Test')->first();
        $this->assertTrue($result->parent_id>1);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withArray_no_parent() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'addTagByString',['Test']);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withArray_parent() {
        $test = new TagManager();
        $this->callProtectedMethod($test,'addTagByString',['TagA.Test']);
        $result = DB::table('tags')->where('name','Test')->first();
        $this->assertEquals(1,$result->parent_id);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withDescriptor_no_parent() {
        $test = new TagManager();
        $Descriptor = new Descriptor();
        $Descriptor->name = 'Test';
        $Descriptor->parent = 'TagA';
        $this->callProtectedMethod($test,'addTagByDescriptor',[$Descriptor]);
        $result = DB::table('tags')->where('name','Test')->get();
        $this->assertTrue($result->count()>0);
    }
    
    /**
     * @group add
     */
    public function testAddTag_withDescriptor_parent() {
        $test = new TagManager();
        $Descriptor = new Descriptor();
        $Descriptor->name = 'Test';
        $Descriptor->parent = 'TagA';
        $this->callProtectedMethod($test,'addTagByDescriptor',[$Descriptor]);
        $result = DB::table('tags')->where('name','Test')->first();
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
        Tags::addTag($tag);
        $result = DB::table('tags')->where('name',$expect)->get();
        $this->assertEquals($expect,$result[0]->name);
    }

    public function AddTagProvider() {
        return [
            ['Test','Test'],
            ['TagA.Test','Test'],
            [['name'=>'Test'],'Test'],
            [function() { $Descriptor = new Descriptor(); $Descriptor->name = 'Test'; return $Descriptor; },'Test'],
            [function() { $tag = new Tag(); $tag->setName('Test'); return $tag; },'Test'],
        ];
    }                                                                
                                                                
    // List all tags with a condition
    /**
     * @group list
     */
    public function testListConiditional() {
        
        $result = Tags::listTags("name<'TagC'");
        $this->assertEquals('TagB',$result[1]->name);
    }

    // List all tags with delta and limit
    /**
     * @group list
     */
    public function testListConiditionalWithDelta() {
        
        $result = Tags::listTags("name<'TagC'",1,1);
        $this->assertEquals('TagB',$result[0]->name);
    }
                                                                                                    
    // Search a tag with unique name
    /**
     * @group search
     */
    public function testSearchUnique() {
        
        $result = Tags::searchTag('TagA');
       $this->assertEquals(1,$result->id);
    }
    
    // Search a tag with multiple resuslts
    /**
     * @group search
     */
    public function testSearchMultiple() {
        
        $result = Tags::searchTag('TagE');
        $this->assertEquals(5,$result[0]->id);        
    }
    
    // Search a tag with no result
    /**
     * @group search
     */
    public function testSearchNoResult() {
        
        $result = Tags::searchTag('NonExistingTag');
        $this->assertNull($result);
    }
    
    /**
     * @dataProvider QueryProvider
     * @group query
     */
    public function testQuery($callback, $modifier, $expect)
    {
        $query = Tags::query();
        $result = $callback($query);
        
        if (is_callable($modifier)) {
            $result = $modifier($result);
        }
        $this->assertEquals($expect, $result);
    }
    
    public function QueryProvider()
    {
        return [
            [function($query) { return $query->count(); }, null, 9],
            [function($query) { return $query->first(); }, function($value){ return $value->name; }, 'TagA'],            
            [function($query) { return $query->offset(5)->first(); }, function($value) { return $value->name; }, 'TagF'],
            [function($query) { return $query->orderBy('name')->offset(5)->first(); }, function($value) { return $value->name; }, 'TagE'],
            [function($query) { return $query->where('name','TagC')->first(); }, function($value){ return $value->name; }, 'TagC'],            
            [function($query) { return $query->where('parent_id','<>',0)->first(); }, function($value){ return $value->name; }, 'TagC'],
            [function($query) { return $query->where('parent','=','TagF')->get(); }, function($value){ return $value[0]->name; }, 'TagG'],
            [function($query) { return $query->where('is assigned')->get(); }, function($value){ return $value[0]->name; }, 'TagA'],
            [function($query) { return $query->where('not assigned')->get(); }, function($value){ return $value[2]->name; }, 'TagZ'],
            ];
    }
 
    /**
     * @group gettags
     */
    public function testGetTags()
    {
        $query = Tags::query()->getTags();
        $this->assertEquals(9,count($query));
        $this->assertEquals(1,$query[0]->getID());
    }
}
