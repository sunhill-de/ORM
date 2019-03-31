<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PropertyTest extends TestCase
{
	/**
	 * Testet den Konstruktur der Property.
	 *
	 * @return void
	 */
	public function testPropertyConstructor()
	{
		$test = new \Sunhill\Properties\oo_property(null);
		$this->assertFalse(is_null($test));
		return $test;
	}
	
	/**
	 * Testet die Zuweisung eines Default-Wertes
	 * @depends testPropertyConstructor
	 */
	public function testPropertyDefault($test) {
		$test->set_default('ABC');
		$this->assertEquals('ABC',$test->get_value());
		return $test;
	}
	
	/**
	 * Testet die Wertzuweisung
	 * @depends testPropertyDefault
	 */
	public function testPropertySetValue($test) {
	    $test->set_value('DEF');
		$this->assertEquals('DEF',$test->get_value());
		return $test;
	}
	
	/**
	 * Testet, ob eine Wertzuweisung die Property dirty macht
	 * @depends testPropertySetValue
	 */
	public function testPropertyDirty($test) {
		$this->assertTrue($test->get_dirty());
		return $test;
	}
	
	/**
	 * Testet, ob der Wert nach Rollback wiederhergestellt ist
	 * @depends testPropertyDirty
	 */
	public function testRollback($test) {
		$test->rollback();
		$this->assertEquals('ABC',$test->get_value());
		return $test;
	}
	
	/**
	 * Testet, ob die Property nach dem Rollback immer noch dirty ist
	 * @depends testRollback
	 */
	public function testUndirtyAfterRollback($test) {
		$this->assertFalse($test->get_dirty());
		return $test;
	}
	
	/**
	 * Testet, ob die Property nach einem Commit noch dirty ist
	 * @depends testUndirtyAfterRollback
	 */
	public function testUndirtyAfterCommit($test) {
		$test->set_value('GHI');
		$test->commit();
		$this->assertFalse($test->get_dirty());
		return $test;
	}
	
	/**
	 * Testet, ob ein Rollback nach Commit etwas bewirkt
	 * @depends testUndirtyAfterRollback
	 */
	public function testRollbackAfterCommit($test) {
		$test->rollback();
		$this->assertEquals('GHI',$test->get_value());
		return $test;
	}
	
	/**
	 * Testet einen Doppelten Commit
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
	 * Testet einen Doppelten Commit
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
	 * Testet, ob die Zuweisung des gleichen Wertes, dirty ändert
	 * @depends testDoubleChange
	 */
	public function testDirtyIfNoChange($test) {
		$test->set_value('XYZ');
		$this->assertFalse($test->get_dirty());
	}
	
	/**
	 * Testet, ob der Vergleich auf gleichheit auch für Objekte gilt
	 */
	public function testDirtyIfNoChangeWithObjects() {
//		$object = new \Sunhill\Objects\oo_object();
		$value = new \Sunhill\Objects\oo_object();
		$test = new \Sunhill\Properties\oo_property(null);
		$test->set_value($value);
		$test->commit();
		$test->set_value($value);
		$this->assertFalse($test->get_dirty());
	}
	
	/**
	 * Testet, ob commit mit Default korrekt ist
	 */
	public function testCommitUninitialiedWithDefault() {
		$test = new \Sunhill\Properties\oo_property(null);
		$test->set_default('ERF');
		$test->commit();
		$this->assertEquals('ERF',$test->get_value());
	}
	
	/**
	 * Testet, ob commit ohne Default korrekt ist
	 * @expectedException \Exception
	 */
	public function testCommitUninitialiedWithoutDefault() {
		$test = new \Sunhill\Properties\oo_property(null);
		$test->commit();
	}
	
	/**
	 * Testet, ob eine Exception ausgelöst wird, wenn ein nicht initialisierter Wert gelesen wird
	 * @expectedException \Exception
	 */
	public function testExceptionUninitialized() {
		$test = new \Sunhill\Properties\oo_property(null);
		$wert = $test->get_value();
	}
	
	/**
	 * Testet, ob das setzen von Null Werten korrekt gehandelt wird
	 */
	public function testSetNullDirty() {
		$test = new \Sunhill\Properties\oo_property(null);
		$test->set_value(null);
		$this->assertTrue($test->get_dirty());
		return $test;
	}
	
	/**
	 * Testet, ob das setzen von Null Werten korrekt gehandelt wird
	 * @depends testSetNullDirty
	 */
	public function testSetNullInitialized($test) {
		$test->set_value(null);
		$this->assertEquals(null,$test->get_value());
		return $test;
	}
	
	/**
	 * Testet, ob das setzen von Null Werten korrekt gehandelt wird
	 */
	public function testSetToNullAgain() {
		$test = new \Sunhill\Properties\oo_property(null);
		$test->set_value('ABC');
		$test->commit();
		$test->set_value(null);
		$this->assertEquals(null,$test->get_value());
		return $test;
	}
	
	public function testDefaultsNull() {
		$test = new \Sunhill\Properties\oo_property(null);
		$test->set_default(null);
		$this->assertTrue(is_null($test->value));
	}
}
