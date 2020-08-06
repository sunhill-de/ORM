<?php

namespace Tests\Feature;

use Sunhill\Test\sunhill_testcase_db;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

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
	
	protected function setUp():void {
		parent::setUp();
		$this->prepare_tables();
	//	$this->artisan('migrate:refresh', ['--seed'=>true]);
	}
	
	/**
	 * @group static
     * Dieser Test wurde in Feature ausgelager, da er von update-commits abhängig ist
	 */
	public function testStaticDeleteTagObjects() {
	    $this->prepare_tables();
	    $this->create_write_scenario();
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
	
}
