<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Illuminate\Support\Facades\DB;

class TagTest extends \Tests\sunhill_testcase
{
	
	use \Tests\DatabaseSetup;
	
	protected function setUp():void {
		parent::setUp();
		$this->clear_system_tables();
		$this->seed();
	//	$this->artisan('migrate:refresh', ['--seed'=>true]);
	}
	
	public function testLoadTag()
    {
    	$tag = new \Sunhill\Objects\oo_tag(1); 
		$this->assertEquals('TagA',$tag->get_name());
	}
	
	public function testLoadFullpath()
	{
		$tag = new \Sunhill\Objects\oo_tag(1);
		$this->assertEquals('TagA',$tag->get_fullpath());
	}
	
	public function testLoadTagWithParent()
	{
		$tag = new \Sunhill\Objects\oo_tag(2);
		$this->assertEquals('TagChildA',$tag->get_name());
	}
	
	public function testLoadTagWithParentFullpath()
	{
		$tag = new \Sunhill\Objects\oo_tag(5);
		$this->assertEquals('TagB.TagChildB.TagChildB',$tag->get_fullpath());
	}
	
	public function testStoreTag()
	{
		$tag = new \Sunhill\Objects\oo_tag();
		$tag->set_name('TestTag');
		$tag->commit();
		$read = \App\tag::where('name','=','TestTag')->first();
		$this->assertFalse(is_null($read));
	}

	public function testEditTag()
	{
		$tag = new \Sunhill\Objects\oo_tag(1);
		$tag->set_name('TestTag');
		$tag->commit();
		$read = new \Sunhill\Objects\oo_tag(1);
		$this->assertEquals('TestTag',$read->get_name());
	}
	
	public function testSearchTag() {
		$tag = new \Sunhill\Objects\oo_tag('TagA');
		$this->assertEquals(1,$tag->get_id());
	}
	
	public function testSearchTagWithParent() {
		$tag = new \Sunhill\Objects\oo_tag('TagB.TagChildB');
		$this->assertEquals(4,$tag->get_id());
	}
	
	public function testSearchTagWithParentUnique() {
		$tag = new \Sunhill\Objects\oo_tag('TagChildA');
		$this->assertEquals(2,$tag->get_id());
	}
	
	public function testAddTagWithAutocreateSimple() {
		$tag = new \Sunhill\Objects\oo_tag('AutoTag',true);
		$read = new \Sunhill\Objects\oo_tag($tag->get_id());
		$this->assertEquals('AutoTag',$read->name);
	}

	public function testAddTagWithAutocreateWithParent() {
		$tag = new \Sunhill\Objects\oo_tag('TagA.AutoTagA',true);
		$read = new \Sunhill\Objects\oo_tag($tag->get_id());
		$this->assertEquals(1,$read->get_parent()->get_id());
	}
	
	public function testAddTagWithAutocreateRecursive() {
		$tag = new \Sunhill\Objects\oo_tag('TagB.AutoTagB.AutoChildB',true);
		$read = new \Sunhill\Objects\oo_tag($tag->get_id());
		$this->assertEquals(3,$read->get_parent()->get_parent()->get_id());
	}
		
	/**
	 * @expectedException \Exception
	 */
	public function testNotFound() {
		$tag = new \Sunhill\Objects\oo_tag('notfound');		
	}
	
	/**
	 * @expectedException \Exception
	 */
	public function testNotUnique() {
		$tag = new \Sunhill\Objects\oo_tag('TagChildB');
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagPass() {
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagA');
	    $this->assertEquals(1,$tag->get_id());
	}

	/**
	 * @group static
	 */
	public function testStaticSearchTagWithParent() {
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagB.TagChildB');
	    $this->assertEquals(4,$tag->get_id());
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagFail() {
	    $tag = \Sunhill\Objects\oo_tag::search_tag('notexisting');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagMultiple() {
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagChildB');
	    $this->assertTrue(is_array($tag));
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagNoParentPass() {
	    \Sunhill\Objects\oo_tag::add_tag('addtagtest');
	    $tag = new \Sunhill\Objects\oo_tag('addtagtest');
	    $this->assertNotNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagParentPass() {
	    \Sunhill\Objects\oo_tag::add_tag('addtagparent.addtagtest2');
	    $tag = new \Sunhill\Objects\oo_tag('addtagparent.addtagtest2');
	    $this->assertNotNull($tag->get_parent());
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagCache() {
	    \Sunhill\Objects\oo_tag::add_tag('addtagparent.addtagtest3');
	    $tag = new \Sunhill\Objects\oo_tag('addtagtest3');
	    $this->assertNotNull($tag->get_parent());	    
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTag() {
	    $this->BuildTestClasses();
	    $this->clear_system_tables();
	    $this->seed();
	    \Sunhill\Objects\oo_tag::delete_tag('TagA');
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagA');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagTable() {
	    $this->BuildTestClasses();
	    $this->clear_system_tables();
	    $this->seed();
	    \Sunhill\Objects\oo_tag::delete_tag('TagA');
        $result = DB::table('tags')->where('name','=','TagA')->first();
        $this->assertNull($result);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagcacheTable() {
	    $this->BuildTestClasses();
	    $this->clear_system_tables();
	    $this->seed();
	    \Sunhill\Objects\oo_tag::delete_tag('TagA');
	    $result = DB::table('tagcache')->where('tag_id','=',1)->first();
	    $this->assertNull($result);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagObjects() {
	    $this->BuildTestClasses();
	    $this->clear_system_tables();
	    $this->seed();
	    $object = new \Sunhill\Test\ts_dummy(); 
	    $object->dummyint = 1;
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagA');
	    $object->tags->stick($tag);
	    $object->commit();
	    $tag = \Sunhill\Objects\oo_tag::delete_tag('TagA');
	    \Sunhill\Objects\oo_object::flush_cache();
	    $object = \Sunhill\Objects\oo_object::load_object_of($object->get_id());
	    $this->assertEquals(0,count($object->tags));
	}
	
	/**
	 * @group static
	 */
	public function testStaticTree() {
	    $this->BuildTestClasses();
	    $this->clear_system_tables();
	    $this->seed();
	    $tree = \Sunhill\Objects\oo_tag::tree_tags();
	    $this->assertEquals([['name'=>'TagA','children'=>
	                               [['name'=>'TagChildA','children'=>[]]]],
	                         ['name'=>'TagB','children'=>
	                               [['name'=>'TagChildB','children'=>
	                                   [['name'=>'TagChildB','children'=>[]]]
	                               ]]
	                        ]
	    ],$tree);	    
	}
	
	/**
	 * @group static
	 */
	public function testStaticOrphans() {
	    $this->BuildTestClasses();
	    $this->clear_system_tables();
	    $this->seed();
	    $object = new \Sunhill\Test\ts_dummy();
	    $object->dummyint = 1;
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagA.TagChildA');
	    $object->tags->stick($tag);
	    $object->commit();
	    $orphans = \Sunhill\Objects\oo_tag::get_orphaned_tags();
	    $this->assertEquals(['TagB.TagChildB.TagChildB'],$orphans);
	}
}
