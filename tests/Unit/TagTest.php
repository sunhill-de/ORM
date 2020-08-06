<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Illuminate\Support\Facades\DB;
use Sunhill\Test\sunhill_testcase_db;

class TagTest extends sunhill_testcase_db
{
	
	use \Tests\DatabaseSetup;
	
	protected function prepare_tables() {
	    parent::prepare_tables();
	    $this->create_special_table('dummies');
	    $this->create_special_table('passthrus');
	    $this->create_special_table('testparents');
	    $this->create_special_table('testchildren');
	    $this->create_special_table('referenceonlies');
	}
	
	protected function setup_scenario()  {
	    $this->prepare_tables();
	    $this->create_write_scenario();	    
	}
	
	public function testLoadTag()
    {
        $this->setup_scenario();
        $tag = new \Sunhill\Objects\oo_tag(1); 
		$this->assertEquals('TagA',$tag->get_name());
	}
	
	public function testLoadFullpath()
	{
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag(1);
		$this->assertEquals('TagA',$tag->get_fullpath());
	}
	
	public function testLoadTagWithParent()
	{
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag(3);
		$this->assertEquals('TagC',$tag->get_name());
	}
	
	public function testLoadTagWithParentFullpath()
	{
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag(3);
		$this->assertEquals('TagB.TagC',$tag->get_fullpath());
	}
	
	public function testStoreTag()
	{
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag();
		$tag->set_name('TestTag');
		$tag->commit();
		$read = DB::table('tags')->where('name','=','TestTag')->first();
		$this->assertFalse(is_null($read));
	}

	public function testEditTag()
	{
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag(1);
		$tag->set_name('TestTag');
		$tag->commit();
		$read = new \Sunhill\Objects\oo_tag(1);
		$this->assertEquals('TestTag',$read->get_name());
	}
	
	public function testSearchTag() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('TagA');
		$this->assertEquals(1,$tag->get_id());
	}
	
	public function testSearchTagWithParent() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('TagB.TagC');
		$this->assertEquals(3,$tag->get_id());
	}
	
	public function testSearchTagWithParentUnique() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('TagC');
		$this->assertEquals(3,$tag->get_id());
	}
	
	public function testAddTagWithAutocreateSimple() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('AutoTag',true);
		$read = new \Sunhill\Objects\oo_tag($tag->get_id());
		$this->assertEquals('AutoTag',$read->name);
	}

	public function testAddTagWithAutocreateWithParent() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('TagA.AutoTagA',true);
		$read = new \Sunhill\Objects\oo_tag($tag->get_id());
		$this->assertEquals(1,$read->get_parent()->get_id());
	}
	
	public function testAddTagWithAutocreateRecursive() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('TagB.AutoTagB.AutoChildB',true);
		$read = new \Sunhill\Objects\oo_tag($tag->get_id());
		$this->assertEquals(2,$read->get_parent()->get_parent()->get_id());
	}
		
	/**
	 * @expectedException \Exception
	 */
	public function testNotFound() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('notfound');		
	}
	
	/**
	 * @expectedException \Exception
	 */
	public function testNotUnique() {
	    $this->setup_scenario();
	    $tag = new \Sunhill\Objects\oo_tag('TagChildB');
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagPass() {
	    $this->setup_scenario();
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagA');
	    $this->assertEquals(1,$tag->get_id());
	}

	/**
	 * @group static
	 */
	public function testStaticSearchTagWithParent() {
	    $this->setup_scenario();
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagB.TagC');
	    $this->assertEquals(3,$tag->get_id());
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagFail() {
	    $this->setup_scenario();
	    $tag = \Sunhill\Objects\oo_tag::search_tag('notexisting');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticSearchTagMultiple() {
	    $this->setup_scenario();
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagChildB');
	    $this->assertTrue(is_array($tag));
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagNoParentPass() {
	    $this->setup_scenario();
	    \Sunhill\Objects\oo_tag::add_tag('addtagtest');
	    $tag = new \Sunhill\Objects\oo_tag('addtagtest');
	    $this->assertNotNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagParentPass() {
	    $this->setup_scenario();
	    \Sunhill\Objects\oo_tag::add_tag('addtagparent.addtagtest2');
	    $tag = new \Sunhill\Objects\oo_tag('addtagparent.addtagtest2');
	    $this->assertNotNull($tag->get_parent());
	}
	
	/**
	 * @group static
	 */
	public function testStaticAddTagCache() {
	    $this->setup_scenario();
	    \Sunhill\Objects\oo_tag::add_tag('addtagparent.addtagtest3');
	    $tag = new \Sunhill\Objects\oo_tag('addtagtest3');
	    $this->assertNotNull($tag->get_parent());	    
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTag() {
	    $this->setup_scenario();
	    \Sunhill\Objects\oo_tag::delete_tag('TagA');
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagA');
	    $this->assertNull($tag);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagTable() {
	    $this->setup_scenario();
	    \Sunhill\Objects\oo_tag::delete_tag('TagA');
        $result = DB::table('tags')->where('name','=','TagA')->first();
        $this->assertNull($result);
	}
	
	/**
	 * @group static
	 */
	public function testStaticDeleteTagEraseTagcacheTable() {
	    $this->setup_scenario();
	    \Sunhill\Objects\oo_tag::delete_tag('TagA');
	    $result = DB::table('tagcache')->where('tag_id','=',1)->first();
	    $this->assertNull($result);
	}
	
	/**
	 * @group static
	 */
	public function testStaticTree() {
	    $this->setup_scenario();
	    $tree = \Sunhill\Objects\oo_tag::tree_tags();
	    $this->assertEquals([
	               ['name'=>'TagA','children'=>[]	             
	         ],[
	               ['name'=>'TagB','children'=>[]],
	               ['name'=>'TagC','children'=>[]]
	         ],[
	             ['name'=>'TagD','children'=>[]]
	         ],[
	             ['name'=>'TagE','children'=>[]]
	         ],[
	             ['name'=>'TagF','children'=>[]]
	         ],
	    ],$tree);	    
	}
	
	/**
	 * @group static
	 */
	public function testStaticOrphans() {
	    $this->setup_scenario();
	    $object = new \Sunhill\Test\ts_dummy();
	    $object->dummyint = 1;
	    $tag = \Sunhill\Objects\oo_tag::search_tag('TagB.TagC');
	    $object->tags->stick($tag);
	    $object->commit();
	    $orphans = \Sunhill\Objects\oo_tag::get_orphaned_tags();
	    $this->assertEquals(['TagA','TagD','TagE','TagF'],$orphans);
	}
}
