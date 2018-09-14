<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;

class TagTest extends TestCase
{
	
	use \Tests\DatabaseSetup;
	
	protected function setUp():void {
		parent::setUp();
		$this->artisan('migrate:refresh', ['--seed'=>true]);
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
}
