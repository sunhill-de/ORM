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
		$test->setDefault('ABC');
		$this->assertEquals('ABC',$test->getValue());
		return $test;
	}
	
	/**
	 * Tests assign of a value
	 * @depends testPropertyDefault
	 */
	public function testPropertySetValue($test) {
	    $test->setValue('DEF');
		$this->assertEquals('DEF',$test->getValue());
		return $test;
	}
	
	/**
	 * Tests if a value assign make property "dirty"
	 * @depends testPropertySetValue
	 */
	public function testPropertyDirty($test) {
		$this->assertTrue($test->getDirty());
		return $test;
	}
	
	/**
	 * Tests if a rollback restores old value
	 * @depends testPropertyDirty
	 */
	public function testRollback($test) {
		$test->rollback();
		$this->assertEquals('ABC',$test->getValue());
		return $test;
	}
	
	/**
	 * Tests if a rollback make property "clean" again
	 * @depends testRollback
	 */
	public function testUndirtyAfterRollback($test) {
		$this->assertFalse($test->getDirty());
		return $test;
	}
	
	/**
	 * Tests if a commit makes property "clean"
	 * @depends testUndirtyAfterRollback
	 */
	public function testUndirtyAfterCommit($test) {
		$test->setValue('GHI');
		$test->commit();
		$this->assertFalse($test->getDirty());
		return $test;
	}
	
	/**
	 * Tests if a rollback after a commit does anything unexpected
	 * @depends testUndirtyAfterRollback
	 */
	public function testRollbackAfterCommit($test) {
		$test->rollback();
		$this->assertEquals('GHI',$test->getValue());
		return $test;
	}
	
	/**
	 * Tests a double commit
	 * @depends testRollbackAfterCommit
	 */
	public function testDoubleCommit($test) {
		$test->setValue('XYZ');
		$test->commit();
		$test->setValue('ZXY');
		$test->commit();
		$this->assertEquals('ZXY',$test->getValue());
		return $test;
	}
	
	/**
	 * Tests a rollback after two value assigns
	 * @depends testDoubleCommit
	 */
	public function testDoubleChange($test) {
		$test->setValue('XYZ');
		$test->commit();
		$test->setValue('ZXY');
		$test->setValue('FOO');
		$test->rollback();
		$this->assertEquals('XYZ',$test->getValue());
		return $test;
	}
	
	/**
	 * Tests if a assign of the same value makes the property dirty
	 * @depends testDoubleChange
	 */
	public function testDirtyIfNoChange($test) {
		$test->setValue('XYZ');
		$this->assertFalse($test->getDirty());
	}
	
	/**
	 * Tests the same as above for objects
	 */
	public function testDirtyIfNoChangeWithObjects() {
//		$object = new ORMObject();
		$value = new ORMObject();
		$test = new Property(null);
		$test->setValue($value);
		$test->commit();
		$test->setValue($value);
		$this->assertFalse($test->getDirty());
	}
	
	/**
	 * Tests if a commit with a default value is ok
	 */
	public function testCommitUninitialiedWithDefault() {
	    $test = new Property(null);
		$test->setDefault('ERF');
		$test->commit();
		$this->assertEquals('ERF',$test->getValue());
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
		$wert = $test->getValue();
	}
	
	/**
	 * Tests if assign of null makes property dirty
	 */
	public function testSetNullDirty() {
		$test = new Property(null);
		$test->setValue(null);
		$this->assertTrue($test->getDirty());
		return $test;
	}
	
	/**
	 * Tests if assign of null is handled correctly
	 * @depends testSetNullDirty
	 */
	public function testSetNullInitialized($test) {
		$test->setValue(null);
		$this->assertEquals(null,$test->getValue());
		return $test;
	}
	
	/**
	 * Tests if commit of a null value is correct
	 */
	public function testSetToNullAgain() {
		$test = new Property(null);
		$test->setValue('ABC');
		$test->commit();
		$test->setValue(null);
		$this->assertEquals(null,$test->getValue());
		return $test;
	}
	
    /**
     * Tests if defaults null is working
     */
	public function testDefaultsNull() {
		$test = new Property(null);
		$test->setDefault(null);
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
