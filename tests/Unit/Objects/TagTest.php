<?php

namespace Sunhill\ORM\Tests\Unit\Objects;

use Sunhill\ORM\Tests\DBTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Objects\ts_dummy;

class TagTest extends DBTestCase
{
	
	public function testLoadTag()
    {
        $tag = new Tag(1); 
		$this->assertEquals('TagA',$tag->get_name());
	}
	
	public function testLoadFullpath()
	{
	    $tag = new Tag(1);
		$this->assertEquals('TagA',$tag->get_fullpath());
	}
	
	public function testLoadTagWithParent()
	{
	    $tag = new Tag(3);
		$this->assertEquals('TagC',$tag->get_name());
	}
	
	public function testLoadTagWithParentFullpath()
	{
	    $tag = new Tag(3);
		$this->assertEquals('TagB.TagC',$tag->get_fullpath());
	}
	
	public function testStoreTag()
	{
	    $tag = new Tag();
		$tag->set_name('TestTag');
		$tag->commit();
		$read = DB::table('tags')->where('name','=','TestTag')->first();
		$this->assertFalse(is_null($read));
	}

	public function testEditTag()
	{
	    $tag = new Tag(1);
		$tag->set_name('TestTag');
		$tag->commit();
		$read = new Tag(1);
		$this->assertEquals('TestTag',$read->get_name());
	}
	
	public function testSearchTag() {
	    $tag = new Tag('TagA');
		$this->assertEquals(1,$tag->get_id());
	}
	
	public function testSearchTagWithParent() {
	    $tag = new Tag('TagB.TagC');
		$this->assertEquals(3,$tag->get_id());
	}
	
	public function testSearchTagWithParentUnique() {
	    $tag = new Tag('TagC');
		$this->assertEquals(3,$tag->get_id());
	}
	
	public function testAddTagWithAutocreateSimple() {
	    $tag = new Tag('AutoTag',true);
		$read = new Tag($tag->get_id());
		$this->assertEquals('AutoTag',$read->name);
	}

	public function testAddTagWithAutocreateWithParent() {
	    $tag = new Tag('TagA.AutoTagA',true);
		$read = new Tag($tag->get_id());
		$this->assertEquals(1,$read->get_parent()->get_id());
	}
	
	public function testAddTagWithAutocreateRecursive() {
	    $tag = new Tag('TagB.AutoTagB.AutoChildB',true);
		$read = new Tag($tag->get_id());
		$this->assertEquals(2,$read->get_parent()->get_parent()->get_id());
	}
		
	public function testNotFound() {
	    $this->expectException(\Exception::class);
	    $tag = new Tag('notfound');		
	}
	
	public function testNotUnique() {
	    $this->expectException(\Exception::class);
	    $tag = new Tag('TagChildB');
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagPass() {
	    $tag = Tag::searchTag('TagA');
	    $this->assertEquals(1,$tag->get_id());
	}

	/**
	 * @group static
	 */
	public function testStaticSearchTagWithParent() {
	    $tag = Tag::searchTag('TagB.TagC');
	    $this->assertEquals(3,$tag->get_id());
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagFail() {
	    $tag = Tag::searchTag('notexisting');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagMultiple() {
	    $tag = Tag::searchTag('TagE');
	    $this->assertTrue(is_array($tag));
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagNoParentPass() {
	    Tag::addTag('addtagtest');
	    $tag = new Tag('addtagtest');
	    $this->assertNotNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagParentPass() {
	    Tag::addTag('addtagparent.addtagtest2');
	    $tag = new Tag('addtagparent.addtagtest2');
	    $this->assertNotNull($tag->get_parent());
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagCache() {
	    Tag::addTag('addtagparent.addtagtest3');
	    $tag = new Tag('addtagtest3');
	    $this->assertNotNull($tag->get_parent());	    
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTag() {
	    Tag::deleteTag('TagA');
	    $tag = Tag::searchTag('TagA');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagTable() {
	    Tag::deleteTag('TagA');
	    $this->assertDatabaseMissing('tags', ['name'=>'TagA']);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagcacheTable() {
	    Tag::deleteTag('TagA');
	    $this->assertDatabaseMissing('tagcache', ['tag_id'=>1]);
	}
	
	/**
	 * @group static
	 */
	public function testStaticTree() {
	    $tree = Tag::tree_tags();
	    $this->assertEquals(
	        [
	            ['name'=>'TagA','children'=>[]],
	            ['name'=>'TagB','children'=>[
	                ['name'=>'TagC','children'=>[]]
	            ]],
	            ['name'=>'TagD','children'=>[]],
	            ['name'=>'TagE','children'=>[]],
	            ['name'=>'TagF','children'=>[
	                ['name'=>'TagG','children'=>[
	                   ['name'=>'TagE','children'=>[]]    
	                ]]
	            ]]
	        ]
	        
	    ,$tree);	    
	}
	
	/**
	 * @group static
	 */
	public function testStaticOrphans() {
	    $object = new ts_dummy();
	    $object->dummyint = 1;
	    $tag = Tag::searchTag('TagB.TagC');
	    $object->tags->stick($tag);
	    $object->commit();
	    $orphans = Tag::get_orphaned_tags();
	    $this->assertEquals(['TagD','TagE','TagF.TagG.TagE'],$orphans);
	}
}
