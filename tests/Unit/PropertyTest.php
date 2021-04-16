<?php

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Properties\oo_property;
use Sunhill\ORM\Objects\oo_object;

class PropertyTest extends TestCase
{
	/**
	 * Testet den Konstruktur der Property.
	 *
	 * @return void
	 */
	public function testPropertyConstructor()
	{
		$test = new oo_property(null);
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
//		$object = new oo_object();
		$value = new oo_object();
		$test = new oo_property(null);
		$test->set_value($value);
		$test->commit();
		$test->set_value($value);
		$this->assertFalse($test->get_dirty());
	}
	
	/**
	 * Testet, ob commit mit Default korrekt ist
	 */
	public function testCommitUninitialiedWithDefault() {
	    $test = new oo_property(null);
		$test->set_default('ERF');
		$test->commit();
		$this->assertEquals('ERF',$test->get_value());
	}
	
	/**
	 * Testet, ob commit ohne Default korrekt ist
	 */
	public function testCommitUninitialiedWithoutDefault() {
	    $this->expectException(\Exception::class);
	    $test = new oo_property(null);
		$test->commit();
	}
	
	/**
	 * Testet, ob eine Exception ausgelöst wird, wenn ein nicht initialisierter Wert gelesen wird
	 */
	public function testExceptionUninitialized() {
	    $this->expectException(\Exception::class);
	    $test = new oo_property(null);
		$wert = $test->get_value();
	}
	
	/**
	 * Testet, ob das setzen von Null Werten korrekt gehandelt wird
	 */
	public function testSetNullDirty() {
		$test = new oo_property(null);
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
		$test = new oo_property(null);
		$test->set_value('ABC');
		$test->commit();
		$test->set_value(null);
		$this->assertEquals(null,$test->get_value());
		return $test;
	}
	
	public function testDefaultsNull() {
		$test = new oo_property(null);
		$test->set_default(null);
		$this->assertTrue(is_null($test->value));
	}
	
	public function testAdditinalFields() {
	    $test = new oo_property(null);
	    $test->set_additional('ABC')->set_another('DEF');
	    $this->assertEquals('ABC',$test->get_additional());
	    $this->assertEquals('DEF',$test->get_another());
	}
}