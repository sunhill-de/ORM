<?php

namespace Tests\Unit;

use Tests\DBTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\oo_tag;
use Sunhill\ORM\Objects\oo_object;

class TagTest extends DBTestCase
{
	
	public function testLoadTag()
    {
        $tag = new oo_tag(1); 
		$this->assertEquals('TagA',$tag->get_name());
	}
	
	public function testLoadFullpath()
	{
	    $tag = new oo_tag(1);
		$this->assertEquals('TagA',$tag->get_fullpath());
	}
	
	public function testLoadTagWithParent()
	{
	    $tag = new oo_tag(3);
		$this->assertEquals('TagC',$tag->get_name());
	}
	
	public function testLoadTagWithParentFullpath()
	{
	    $tag = new oo_tag(3);
		$this->assertEquals('TagB.TagC',$tag->get_fullpath());
	}
	
	public function testStoreTag()
	{
	    $tag = new oo_tag();
		$tag->set_name('TestTag');
		$tag->commit();
		$read = DB::table('tags')->where('name','=','TestTag')->first();
		$this->assertFalse(is_null($read));
	}

	public function testEditTag()
	{
	    $tag = new oo_tag(1);
		$tag->set_name('TestTag');
		$tag->commit();
		$read = new oo_tag(1);
		$this->assertEquals('TestTag',$read->get_name());
	}
	
	public function testSearchTag() {
	    $tag = new oo_tag('TagA');
		$this->assertEquals(1,$tag->get_id());
	}
	
	public function testSearchTagWithParent() {
	    $tag = new oo_tag('TagB.TagC');
		$this->assertEquals(3,$tag->get_id());
	}
	
	public function testSearchTagWithParentUnique() {
	    $tag = new oo_tag('TagC');
		$this->assertEquals(3,$tag->get_id());
	}
	
	public function testAddTagWithAutocreateSimple() {
	    $tag = new oo_tag('AutoTag',true);
		$read = new oo_tag($tag->get_id());
		$this->assertEquals('AutoTag',$read->name);
	}

	public function testAddTagWithAutocreateWithParent() {
	    $tag = new oo_tag('TagA.AutoTagA',true);
		$read = new oo_tag($tag->get_id());
		$this->assertEquals(1,$read->get_parent()->get_id());
	}
	
	public function testAddTagWithAutocreateRecursive() {
	    $tag = new oo_tag('TagB.AutoTagB.AutoChildB',true);
		$read = new oo_tag($tag->get_id());
		$this->assertEquals(2,$read->get_parent()->get_parent()->get_id());
	}
		
	public function testNotFound() {
	    $this->expectException(\Exception::class);
	    $tag = new oo_tag('notfound');		
	}
	
	public function testNotUnique() {
	    $this->expectException(\Exception::class);
	    $tag = new oo_tag('TagChildB');
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagPass() {
	    $tag = oo_tag::search_tag('TagA');
	    $this->assertEquals(1,$tag->get_id());
	}

	/**
	 * @group static
	 */
	public function testStaticSearchTagWithParent() {
	    $tag = oo_tag::search_tag('TagB.TagC');
	    $this->assertEquals(3,$tag->get_id());
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagFail() {
	    $tag = oo_tag::search_tag('notexisting');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagMultiple() {
	    $tag = oo_tag::search_tag('TagE');
	    $this->assertTrue(is_array($tag));
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagNoParentPass() {
	    oo_tag::add_tag('addtagtest');
	    $tag = new oo_tag('addtagtest');
	    $this->assertNotNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagParentPass() {
	    oo_tag::add_tag('addtagparent.addtagtest2');
	    $tag = new oo_tag('addtagparent.addtagtest2');
	    $this->assertNotNull($tag->get_parent());
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagCache() {
	    oo_tag::add_tag('addtagparent.addtagtest3');
	    $tag = new oo_tag('addtagtest3');
	    $this->assertNotNull($tag->get_parent());	    
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTag() {
	    oo_tag::delete_tag('TagA');
	    $tag = oo_tag::search_tag('TagA');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagTable() {
	    oo_tag::delete_tag('TagA');
	    $this->assertDatabaseMissing('tags', ['name'=>'TagA']);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagcacheTable() {
	    oo_tag::delete_tag('TagA');
	    $this->assertDatabaseMissing('tagcache', ['tag_id'=>1]);
	}
	
	/**
	 * @group static
	 */
	public function testStaticTree() {
	    $tree = oo_tag::tree_tags();
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
	    $object = new \Sunhill\ORM\Test\ts_dummy();
	    $object->dummyint = 1;
	    $tag = oo_tag::search_tag('TagB.TagC');
	    $object->tags->stick($tag);
	    $object->commit();
	    $orphans = oo_tag::get_orphaned_tags();
	    $this->assertEquals(['TagD','TagE','TagF.TagG.TagE'],$orphans);
	}
}
