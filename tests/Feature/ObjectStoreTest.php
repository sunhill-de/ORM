<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectStoreTest extends ObjectCommon
{

	public function testStoreSimpleFields()
    {
		$this->setup_scenario();
    	$test = new Sunhill\Test\ts_testparent();
		$test->parentint = 1;
		$test->parentchar = 'A';
		$test->parentfloat = 1.1;
		$test->parenttext = 'ABC DEF';
		$test->parentdatetime = '2001-01-01 01:01:01';
		$test->parentdate = '2011-01-01';
		$test->parenttime = '11:11:11';
		$test->parentenum = 'testA';
		$test->commit();		
		$this->assertGreaterThan(0, $id = $test->get_id());
		return $id;
	}
	
	public function testStoreSimpleFieldsChild()
	{
		$test = new \Sunhill\Objects\ts_testchild();
		$test->parentint = 1;
		$test->parentchar = 'A';
		$test->parentfloat = 1.1;
		$test->parenttext = 'ABC DEF';
		$test->parentdatetime = '2001-01-01 01:01:01';
		$test->parentdate = '2011-01-01';
		$test->parenttime = '11:11:11';
		$test->parentenum = 'testA';
		
		$test->childint = 2;
		$test->childchar = 'B';
		$test->childfloat = 2.2;
		$test->childtext = 'DEF GHI';
		$test->childdatetime = '2002-02-02 02:02:02';
		$test->childdate = '2022-02-02';
		$test->childtime = '22:22:22';
		$test->childenum = 'testB';
		$test->commit();
		$this->assertGreaterThan(0, $id = $test->get_id());
		return $id;
	}
	
	public function testStoreComplexFields()
	{
		$test = new \Sunhill\Objects\ts_testparent();
		$obj1 = new \Sunhill\Objects\ts_testparent();
		$obj1->parentint = 2;
		$obj2 = new \Sunhill\Objects\ts_testparent();
		$obj2->parentint = 3;
		$obj3 = new \Sunhill\Objects\ts_testparent();
		$obj3->parentint = 4;
		$test->parentobject = $obj1;
		$test->parentsarray[] = 'A';
		$test->parentsarray[] = 'B';
		$test->parentoarray[] = $obj2;
		$test->parentoarray[] = $obj3;
		$test->parentint = 1;
		$test->parentchar = 'A';
		$test->parentfloat = 1.1;
		$test->parenttext = 'ABC DEF';
		$test->parentdatetime = '2001-01-01 01:01:01';
		$test->parentdate = '2011-01-01';
		$test->parenttime = '11:11:11';
		$test->parentenum = 'testA';
		$test->commit();
		$this->assertGreaterThan(0, $id = $test->get_id());
		return $id;
	}
	
}
