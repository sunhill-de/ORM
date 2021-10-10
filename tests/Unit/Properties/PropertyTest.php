<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Objects\ORMObject;

class PropertyTest extends TestCase
{
	/**
	 * Tests the constructor of the property
	 *
	 * @return void
	 */
	public function testPropertyConstructor()
	{
		$test = new Property(null);
		$this->assertFalse(is_null($test));
		return $test;
	}
	
	/**
	 * Tests assign of a default value
	 * @depends testPropertyConstructor
	 */
	public function testPropertyDefault($test) {
		$test->set_default('ABC');
		$this->assertEquals('ABC',$test->get_value());
		return $test;
	}
	
	/**
	 * Tests assign of a value
	 * @depends testPropertyDefault
	 */
	public function testPropertySetValue($test) {
	    $test->set_value('DEF');
		$this->assertEquals('DEF',$test->get_value());
		return $test;
	}
	
	/**
	 * Tests if a value assign make property "dirty"
	 * @depends testPropertySetValue
	 */
	public function testPropertyDirty($test) {
		$this->assertTrue($test->get_dirty());
		return $test;
	}
	
	/**
	 * Tests if a rollback restores old value
	 * @depends testPropertyDirty
	 */
	public function testRollback($test) {
		$test->rollback();
		$this->assertEquals('ABC',$test->get_value());
		return $test;
	}
	
	/**
	 * Tests if a rollback make property "clean" again
	 * @depends testRollback
	 */
	public function testUndirtyAfterRollback($test) {
		$this->assertFalse($test->get_dirty());
		return $test;
	}
	
	/**
	 * Tests if a commit makes property "clean"
	 * @depends testUndirtyAfterRollback
	 */
	public function testUndirtyAfterCommit($test) {
		$test->set_value('GHI');
		$test->commit();
		$this->assertFalse($test->get_dirty());
		return $test;
	}
	
	/**
	 * Tests if a rollback after a commit does anything unexpected
	 * @depends testUndirtyAfterRollback
	 */
	public function testRollbackAfterCommit($test) {
		$test->rollback();
		$this->assertEquals('GHI',$test->get_value());
		return $test;
	}
	
	/**
	 * Tests a double commit
	 * @depends testRollbackAfterCommit
	 */
	public function testDoubleCommit($test) {
		$test->set_value('XYZ');
		$test->commit();
		$test->set_value('ZXY');
		$test->commit();
		$this->assertEquals('ZXY',$test->get_value());
		return $test;
	}
	
	/**
	 * Tests a rollback after two value assigns
	 * @depends testDoubleCommit
	 */
	public function testDoubleChange($test) {
		$test->set_value('XYZ');
		$test->commit();
		$test->set_value('ZXY');
		$test->set_value('FOO');
		$test->rollback();
		$this->assertEquals('XYZ',$test->get_value());
		return $test;
	}
	
	/**
	 * Tests if a assign of the same value makes the property dirty
	 * @depends testDoubleChange
	 */
	public function testDirtyIfNoChange($test) {
		$test->set_value('XYZ');
		$this->assertFalse($test->get_dirty());
	}
	
	/**
	 * Tests the same as above for objects
	 */
	public function testDirtyIfNoChangeWithObjects() {
//		$object = new ORMObject();
		$value = new ORMObject();
		$test = new Property(null);
		$test->set_value($value);
		$test->commit();
		$test->set_value($value);
		$this->assertFalse($test->get_dirty());
	}
	
	/**
	 * Tests if a commit with a default value is ok
	 */
	public function testCommitUninitialiedWithDefault() {
	    $test = new Property(null);
		$test->set_default('ERF');
		$test->commit();
		$this->assertEquals('ERF',$test->get_value());
	}
	
	/**
	 * Tests if a commit with no default value raises an exception
	 */
	public function testCommitUninitialiedWithoutDefault() {
	    $this->expectException(\Exception::class);
	    $test = new Property(null);
		$test->commit();
	}
	
	/**
	 * Tests if a read on a uninitialied value raises an exception
	 */
	public function testExceptionUninitialized() {
	    $this->expectException(\Exception::class);
	    $test = new Property(null);
		$wert = $test->get_value();
	}
	
	/**
	 * Tests if assign of null makes property dirty
	 */
	public function testSetNullDirty() {
		$test = new Property(null);
		$test->set_value(null);
		$this->assertTrue($test->get_dirty());
		return $test;
	}
	
	/**
	 * Tests if assign of null is handled correctly
	 * @depends testSetNullDirty
	 */
	public function testSetNullInitialized($test) {
		$test->set_value(null);
		$this->assertEquals(null,$test->get_value());
		return $test;
	}
	
	/**
	 * Tests if commit of a null value is correct
	 */
	public function testSetToNullAgain() {
		$test = new Property(null);
		$test->set_value('ABC');
		$test->commit();
		$test->set_value(null);
		$this->assertEquals(null,$test->get_value());
		return $test;
	}
	
    /**
     * Tests if defaults null is working
     */
	public function testDefaultsNull() {
		$test = new Property(null);
		$test->set_default(null);
		$this->assertTrue(is_null($test->value));
	}
	
    /**
     * Tests if property can implement additional fields
     */
	public function testAdditinalFields() {
	    $test = new Property(null);
	    $test->set_additional('ABC')->set_another('DEF');
	    $this->assertEquals('ABC',$test->get_additional());
	    $this->assertEquals('DEF',$test->get_another());
	}
}
