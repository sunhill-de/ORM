<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;

class ObjectReReadTest extends ObjectCommon
{

	public function testStoreSimpleFields()
    {
		$this->setup_scenario();
    	$test = new \Sunhill\Test\ts_testparent();
    	$values = array('int'=>1,'char'=>'A','float'=>1.1,'text'=>'ABC DEF','datetime'=>'2001-01-01 01:01:01',
    			        'date'=>'2011-01-01','time'=>'11:11:11','enum'=>'testA');
    	foreach ($values as $key => $value) {
    		$varname = 'parent'.$key;
    		$test->$varname = $value;
    	}
		$test->commit();		
		$test = new \Sunhill\Test\ts_testparent();
		$read->load($test->get_id());
		$reread = array();
		foreach ($values as $key=>$value) {
			$varname = 'parent'.$key;
			$reread[$key] = $read->$varname;
		}
		$this->assertEquals($values, $reread);
	}
	
	public function testStoreSimpleFieldsChild()
	{
		$test = new \Sunhill\Test\ts_testchild();
		$values = array('parentint'=>1,'parentchar'=>'A','parentfloat'=>1.1,'parenttext'=>'ABC DEF','parentdatetime'=>'2001-01-01 01:01:01',
				'parentdate'=>'2011-01-01','parenttime'=>'11:11:11','parentenum'=>'testA','childint'=>2,'childchar'=>'B','childfloat'=>2.2,'childtext'=>'BCD EFG','childdatetime'=>'2002-02-02 02:02:02',
				'childdate'=>'2022-02-02','childtime'=>'22:22:22','childenum'=>'testB');
		foreach ($values as $key => $value) {
			$varname = $key;
			$test->$varname = $value;
		}
		$test->commit();
		$test = new \Sunhill\Test\ts_testchild();
		$read->load($test->get_id());
		$reread = array();
		foreach ($values as $key=>$value) {
			$varname = $key;
			$reread[$key] = $read->$varname;
		}
		$this->assertEquals($values, $reread);
		
	}
	
	public function testStoreComplexFields()
	{
	    $test = new \Sunhill\Test\ts_testparent();
	    $obj1 = new \Sunhill\Test\ts_testparent();
		$obj1->parentint = 2;
		$obj2 = new \Sunhill\Test\ts_testparent();
		$obj2->parentint = 3;
		$obj3 = new \Sunhill\Test\ts_testparent();
		$obj3->parentint = 4;
		$test->parentobject = $obj1;
		$test->parentsarray[] = 'A';
		$test->parentsarray[] = 'B';
		$test->parentoarray[] = $obj2;
		$test->parentoarray[] = $obj3;
		$values = array('int'=>1,'char'=>'A','float'=>1.1,'text'=>'ABC DEF','datetime'=>'2001-01-01 01:01:01',
				'date'=>'2011-01-01','time'=>'11:11:11','enum'=>'testA');
		foreach ($values as $key => $value) {
			$varname = 'parent'.$key;
			$test->$varname = $value;
		}
		$test->commit();
		$this->assertGreaterThan(0, $id = $test->get_id());
		return $id;
	}
	
}
